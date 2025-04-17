<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LandingController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q') ?? '';
        $rank = $request->input('rank');
        $tahun = $request->input('year');

        if (empty($rank) || !is_numeric($rank)) {
            $rank = "9999"; 
        }

        $python = "C:\\Users\\MyBook Z Series\\AppData\\Local\\Microsoft\\WindowsApps\\python.exe";
        $script = base_path("public/query.py");
        $index_file = base_path("public/unair.pkl");

        // Gunakan escapeshellarg agar aman jika query mengandung spasi
        $process = new Process([
            $python,
            $script,
            $index_file,
            $rank,
            $query,
            // Tambahkan tahun jika valid
            ...( (!empty($tahun) && strtolower($tahun) !== 'semua') ? [$tahun] : [] )
        ]);

        // Log::info("Menjalankan perintah Python: " . $process->getCommandLine());
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("Python Error: " . $process->getErrorOutput());
            return response()->json($process->getErrorOutput(), 500);
        }

        // Ambil hasil dan decode JSON
        $output = $process->getOutput();
        $lines = explode("\n", trim($output));
        $results = [];

        foreach ($lines as $line) {
            if (!empty($line)) {
                $results[] = json_decode($line, true);
            }
        }

        // ⛔ Jangan filter lagi di PHP jika sudah dilakukan di Python
        // Tapi kalau masih ingin aman:
        if (!empty($tahun) && strtolower($tahun) !== 'semua') {
            $results = array_filter($results, function ($item) use ($tahun) {
                return isset($item['tahun']) && $item['tahun'] == $tahun;
            });
        }

        // Format hasil ke HTML
        $html = '';
        foreach ($results as $item) {
            $html .= '
            <div class="col-lg-5">
                <div class="card mb-2">
                    <div style="display: flex; flex: 1 1 auto;">
                        <div class="card-body">
                            <h6 class="card-title">
                                <a target="_blank" href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['title']) . '</a>
                            </h6>
                            <p class="card-text">Penulis: ' . htmlspecialchars($item['penulis']) . '</p>
                            <p class="card-text">Tahun: ' . htmlspecialchars($item['tahun']) . '</p>
                            <p class="card-text text-success">Skor: ' . number_format($item['score'], 4) . '</p>
                        </div>
                    </div>
                </div>
            </div>';
        }

        return response()->json($html);
    }

    public function index()
    {
        $jsonPath = base_path('public/hasil_unair.json');
        $tahunList = [];

        if (file_exists($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);

            foreach ($json as $item) {
                if (isset($item['tahun']) && is_numeric($item['tahun'])) {
                    $tahunList[] = $item['tahun'];
                }
            }

            $tahunList = array_unique($tahunList);
            rsort($tahunList);
        }

        return view('landing', [
            'tahunList' => $tahunList
        ]);
    }
}
