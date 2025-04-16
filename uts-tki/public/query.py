import re
import sys
import json
import pickle

# Argument check
if len(sys.argv) != 4:
    print("\nPenggunaan:\n\tpython query.py [index.pkl] [jumlah_hasil] [query string]\n")
    sys.exit(1)

index_path = sys.argv[1]
top_n = int(sys.argv[2])
query_input = sys.argv[3]

# Stopwords opsional (gunakan kalau kamu punya file stopword.txt)
try:
    stopwords = open("stopword.txt", encoding="utf-8").read().split("\n")
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
for word in query_words:
    if word in stopwords or word == "":
        continue

    if word not in index:
        continue

    for doc in index[word]:
        if doc['url'] in result_docs:
            result_docs[doc['url']]['score'] += doc['score']
        else:
            # Buat salinan dict agar tidak refer ke object asli
            result_docs[doc['url']] = doc.copy()

# Konversi ke list dan sorting
sorted_docs = sorted(result_docs.values(), key=lambda d: d['score'], reverse=True)

# Output hasil (maksimal top_n)
for count, doc in enumerate(sorted_docs[:top_n], start=1):
    print(f"Jumlah dokumen ditemukan: {len(result_docs)}", file=sys.stderr)
    print(json.dumps(doc, ensure_ascii=False))

if not result_docs:
    print("Tidak ada hasil yang cocok ditemukan untuk query tersebut.")
