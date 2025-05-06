# Very Simple Spider 2.0 🕷️

A simple yet extensible web crawler written in PHP 8.2+. Supports `robots.txt`, MIME filtering, depth/page/data limits. Perfect for small-scale scraping tasks or ETL integration.

**🔖 Version:** 2.0  
📚 Version 1.0 + article (in Polish): [Scraping a page offline in PHP](https://eskim.pl/pobieranie-strony-offline-w-php/)  
🌍 Author's website: [https://eskim.pl](https://eskim.pl)  
☕ Donate: [https://buymeacoffee.com/eskim](https://buymeacoffee.com/eskim)  
📜 License: [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html)

---

## 📦 Installation

```bash
git clone https://github.com/eskim83/very-simple-spider.git
cd very-simple-spider
composer install
```

## 🚀 Usage (CLI)

```bash
php bin/crawl.php --url=https://example.com [--depth=2] [--silent] [--limit=100] [--max-bytes=500000] [--out=result.json]
```

### Example:
```bash
php bin/crawl.php --url=https://eskim.pl --depth=2 --limit=10 --max-bytes=200000 --out=eskim.json
```

## ⚙️ Options

| Parameter       | Description                                  |
|-----------------|----------------------------------------------|
| `--url`         | Starting URL (required)                      |
| `--depth=N`     | Maximum crawl depth (default: 3)             |
| `--silent`      | Disable logging                              |
| `--limit=N`     | Maximum number of pages to crawl             |
| `--max-bytes=N` | Max total downloaded bytes                   |
| `--out=file.json` | Save crawl results to a JSON file          |

## 🧪 Tests

### Install PHPUnit
```bash
composer require --dev phpunit/phpunit
```

### Run tests:
```bash
php vendor/bin/phpunit --bootstrap tests/bootstrap.php tests/
```

## 📁 Directory Structure

```
bin/             # CLI interface (crawl.php)
src/             # Core classes
tests/           # Unit and integration tests
composer.json    # Autoload and dependencies
README.md
```

## ✅ Features

- Recursive crawling with queue
- `robots.txt` support
- Static file filtering (e.g. js, css, svg, pdf)
- URL filtering via custom callback
- Limit by page count, depth, and total data
- Supports Guzzle and fallback to `file_get_contents`
- Save results to JSON

## 🪛 Requirements

- PHP 8.2+
- Composer
- (Optional) GuzzleHttp

## 🛑 Disclaimer

This tool performs web scraping, which may be considered undesirable by some websites.  
**The author assumes no responsibility for how this software is used, nor for any consequences of its use, including violations of site policies or terms.**

**Use at your own risk.**

## 📝 License

This project is licensed under [GNU GPL v2.0](https://www.gnu.org/licenses/gpl-2.0.html)
