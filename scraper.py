import requests
from bs4 import BeautifulSoup
import json

class UnairScraper:
    def __init__(self):
        self.base_url = "https://repository.unair.ac.id"
        self.search_url = self.base_url + "/cgi/search/simple"
        self.results = []

    def scrape(self, query="skripsi dan tugas akhir", max_pages=5):
        headers = {
            "User-Agent": "Mozilla/5.0"
        }

        page_url = self.search_url + f"?q={query}&_action_search=Search&_order=bytitle&basic_srchtype=ALL&_satisfyall=ALL"
        current_page = 1

        while page_url and current_page <= max_pages:
            print(f"Scraping halaman {current_page}...")
            response = requests.get(page_url, headers=headers, verify=False)
            soup = BeautifulSoup(response.text, "html.parser")

            rows = soup.select("tr.ep_search_result")
            if not rows:
                print("Tidak ada hasil di halaman ini.")
                break

            for row in rows:
                tds = row.find_all("td")
                if len(tds) < 2:
                    continue

                data = {}

                penulis = tds[1].find("span", class_="person_name")
                data["penulis"] = penulis.text.strip() if penulis else "Tidak diketahui"

                tahun_text = tds[1].text
                match = re.search(r"\((\d{4})\)", tahun_text)
                data["tahun"] = match.group(1) if match else "Tidak diketahui"

                judul_tag = tds[1].find("a")
                data["judul"] = judul_tag.text.strip() if judul_tag else "Tanpa judul"
                data["url_detail"] = self.base_url + judul_tag["href"] if judul_tag else None

                pdf_links = tds[2].find_all("a") if len(tds) > 2 else []
                data["pdf_links"] = [
                    self.base_url + link["href"] for link in pdf_links if ".pdf" in link["href"]
                ]

                self.results.append(data)

            # Cari link "Next"
            next_link = soup.find("a", string="Next")
            if next_link and 'href' in next_link.attrs:
                page_url = self.base_url + next_link['href']
                current_page += 1
            else:
                break

    def save(self, filename="hasil_unair.json"):
        with open(filename, "w", encoding="utf-8") as f:
            json.dump(self.results, f, ensure_ascii=False, indent=4)
        print(f"Disimpan ke {filename} - Total: {len(self.results)} data.")

if __name__ == "__main__":
    import re
    scraper = UnairScraper()
    scraper.scrape(max_pages=10)
    scraper.save()
