# Very Simple Spider 2.0 🕷️

Prosty i rozszerzalny pająk do pobierania stron internetowych, napisany w PHP 8.2+. Obsługuje robots.txt, filtrowanie MIME, limitowanie głębokości, liczby stron i rozmiaru danych. Idealny do małych crawlerów lub integracji z systemami ETL.

**🔖 Wersja:** 2.0  
📚 Wersja 1.0 + artykuł: [Pobieranie strony offline w PHP](https://eskim.pl/pobieranie-strony-offline-w-php/)  
🌍 Strona autora: [https://eskim.pl](https://eskim.pl)  
☕ Donate: [https://buymeacoffee.com/eskim](https://buymeacoffee.com/eskim)  
📜 Licencja: [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html)

---

## 📦 Instalacja

```bash
git clone https://github.com/eskim83/very-simple-spider.git
cd very-simple-spider
composer install
```

## 🚀 Użycie (CLI)

```bash
php bin/crawl.php --url=https://example.com [--depth=2] [--silent] [--limit=100] [--max-bytes=500000] [--out=result.json]
```

### Przykład:
```bash
php bin/crawl.php --url=https://eskim.pl --depth=2 --limit=10 --max-bytes=200000 --out=eskim.json
```

## ⚙️ Opcje

| Parametr        | Opis                                      |
|-----------------|-------------------------------------------|
| `--url`         | Adres URL startowy (wymagany)             |
| `--depth=N`     | Maksymalna głębokość linków (domyślnie 3) |
| `--silent`      | Wyłącza logowanie                         |
| `--limit=N`     | Limit liczby stron                        |
| `--max-bytes=N` | Limit rozmiaru pobranych danych (B)       |
| `--out=plik.json` | Zapis wyników crawl'a do pliku JSON     |

## 🧪 Testy

### Instalacja PHPUnit
```bash
composer require --dev phpunit/phpunit
```

### Uruchamianie testów:
```bash
php vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/
```

## 📁 Struktura katalogów

```
bin/             # CLI (crawl.php)
src/             # Klasy Spidera
tests/           # Jednostkowe i integracyjne testy
composer.json    # Autoloading i zależności
README.md
```

## ✅ Funkcje

- Rekurencyjne pobieranie stron
- Obsługa `robots.txt`
- Pomijanie plików statycznych (js, css, svg, pdf itd.)
- Filtr URL-i z callbackiem
- Limitowanie stron, głębokości, danych
- Obsługa Guzzle lub `file_get_contents`
- Zapisywanie wyników do JSON

## 🪛 Wymagania

- PHP 8.2+
- Composer
- (Opcjonalnie) GuzzleHttp

## 🛑 Zrzeczenie odpowiedzialności

Proces pozyskiwania danych z serwisów opiera się na Web Scrapping-u i może być uznawany za szkodliwy przez niektóre strony internetowe.  
**Autor nie ponosi żadnej odpowiedzialności za użycie tego oprogramowania, skutki jego działania ani naruszenie regulaminów zewnętrznych serwisów.**

**Korzystasz na własną odpowiedzialność.**

## 📝 Licencja

Projekt objęty licencją [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html)
