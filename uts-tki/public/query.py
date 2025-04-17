import re
import sys
import json
import pickle
import io



sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

if len(sys.argv) < 4 or len(sys.argv) > 5:
    print("\nPenggunaan:\n\tpython query.py [index.pkl] [jumlah_hasil] [query] [tahun(optional)]\n")
    sys.exit(1)


index_path = sys.argv[1]
top_n = int(sys.argv[2])
if top_n <= 0:
    top_n = 9999
query_input = sys.argv[3]
year_filter = sys.argv[4] if len(sys.argv) == 5 else None


try:
    stopwords = open("public/stopword.txt", encoding="utf-8").read().split("\n")
except:
    stopwords = []

# Clean function seperti di tf-idf.py
def clean_str(text):
    text = (text.encode('ascii', 'ignore')).decode("utf-8")
    text = re.sub(r"&.*?;", "", text)
    text = re.sub(r">", "", text)    
    text = re.sub(r"[\\\]\|\[\@\,\$\%\*\&\\\(\)\":]", "", text)
    text = re.sub(r"-", " ", text)
    text = re.sub(r"\.+", "", text)
    text = re.sub(r"^\s+", "", text)
    text = text.lower()
    return text

query_words = clean_str(query_input).split()

# Load index
with open(index_path, 'rb') as indexdb:
    index = pickle.load(indexdb)

# Cari dokumen yang relevan
result_docs = {}

if not query_words:
    seen_urls = set()
    for word in index:
        for doc in index[word]:
            if year_filter and str(doc.get('tahun')) != str(year_filter):
                continue
            if doc['url'] in seen_urls:
                continue
            result_docs[doc['url']] = doc.copy()
            seen_urls.add(doc['url'])
else:
    # Query tidak kosong → cari berdasarkan keyword
    for word in query_words:
        if word in stopwords or word == "":
            continue
        if word not in index:
            continue
        for doc in index[word]:
            if year_filter and str(doc.get('tahun')) != str(year_filter):
                continue  
            if doc['url'] in result_docs:
                result_docs[doc['url']]['score'] += doc['score']
            else:
                result_docs[doc['url']] = doc.copy()


# Konversi ke list dan sorting
sorted_docs = sorted(result_docs.values(), key=lambda d: d['score'], reverse=True)

# Output hasil (maksimal top_n)
for count, doc in enumerate(sorted_docs[:top_n], start=1):
    print(f"Jumlah dokumen ditemukan: {len(result_docs)}", file=sys.stderr)
    print(json.dumps(doc, ensure_ascii=False))

if not result_docs:
    print("Tidak ada hasil yang cocok ditemukan untuk query tersebut.")
