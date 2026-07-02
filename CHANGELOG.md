# Changelog

Všechny důležité změny v PPL PrestaShop pluginu.

## [1.1.2] - 2026-07-02

### Vylepšení
- Odstranění opcache_reset() a apcu_clear_cache() z clearCache() — zamezení PHP warning na hostinzích s restrict_api

## [1.1.1] - 2026-06-30

### Opravy
- Autentizace PPL API na PHP 7.x — nahrazení file_get_contents() za cURL v getAccessToken().
- Odebrány neplatné parametry konfigurace mapového widgetu — mode a allowedAccessPointTypes, opraven defaultLanguage na defaultLang dle aktuální specifikace PPL widgetu.

## [1.1.0] - 2026-06-04

### Nové funkce
- Implementace nového mapového widgetu PPL pro výběr výdejních míst
- Nastavení a verifikace mapového widgetu podle API klíče

### Opravy
- Oprava pozice u již vytištěných etiket
- Oprava ikony dopravy v kartě PPL administrace
- Oprava deduplikace adres při multistore režimu
- Oprava seznamu povolených zemí
- Oprava přidávání zásilek do dávky

### Vylepšení
- Refaktorizace komunikace s CPL API (operace, normalizéry)
- Refaktorizace validace košíku
- Přidání timeout parametru pro API volání
- Ochrana proti nenalezení modulu v cache PrestaShop
- Odebrání kontroly aktualizací
- Oprava verzování v build scriptu
