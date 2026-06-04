# Changelog

Všechny důležité změny v PPL PrestaShop pluginu.

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
