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
        $query = $request->input('q');
        $rank = $request->input('rank', 5);  // default 5


        $python = "C:\Users\MyBook Z Series\AppData\Local\Microsoft\WindowsApps\python.exe";
        $script = base_path("public/query.py");
        $index_file = base_path("public/unair.pkl");

        $process = new Process([$python, $script, $index_file, $rank, $query]);
        $process->run();

        
        if (!$process->isSuccessful()) {
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

        // Format ke HTML (atau bisa langsung dikembalikan sebagai data)
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
}
