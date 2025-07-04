# TYPO3 Extension: LLMs.txt Generator

## Extension-Name: `llms_txt_generator`

## Funktionsübersicht

### Kernfunktionen

**1. Automatische llms.txt Generierung**
- Erstellt automatisch eine llms.txt im Root-Verzeichnis der Website
- Unterstützt sowohl Index-Version als auch Full-Content-Version
- Berücksichtigt mehrsprachige TYPO3-Installationen

**2. Backend-Modul für Konfiguration**
- Übersichtliches Admin-Interface im TYPO3-Backend
- Einstellungen für Inhaltsauswahl und -filterung
- Preview-Funktion für generierte llms.txt

### Detaillierte Features

#### Content-Auswahl und -Filterung
- **Seitentyp-Filter**: Auswahl welche Seitentypen einbezogen werden (Standard, Sysfolder ausschließen)
- **Kategorie-basierte Auswahl**: Integration mit TYPO3-Kategorien zur gezielten Inhaltsauswahl
- **Prioritäts-System**: Gewichtung von Seiten nach Wichtigkeit
- **Exclude/Include Listen**: Manuelle Steuerung einzelner Seiten
- **Content-Element-Filter**: Bestimmung welche Content-Elemente berücksichtigt werden

#### Template und Struktur
- **Anpassbare Templates**: Fluid-Templates für llms.txt-Struktur
- **Metadaten-Integration**: Automatische Nutzung von Seitentiteln, Beschreibungen, Keywords
- **Hierarchie-Erhaltung**: Berücksichtigung der TYPO3-Seitenstruktur
- **Custom Fields**: Zusätzliche Felder für LLM-spezifische Informationen

#### Multi-Site und Mehrsprachigkeit
- **Site-Configuration**: Pro TYPO3-Site separate llms.txt
- **Sprachversionen**: Automatische Generierung für verschiedene Sprachen (llms-de.txt, llms-en.txt)
- **Domain-Handling**: Korrekte URL-Generierung für Multi-Domain-Setups

#### Technische Implementation

```php
// Beispiel-Konfiguration in ext_conf_template.txt
site_title = string
site_description = text
include_page_types = string
exclude_page_uids = string
max_content_length = int
enable_full_version = boolean
update_frequency = options[manual,daily,weekly]
```

#### Backend-Modul Features
- **Dashboard**: Übersicht über aktuelle llms.txt-Status
- **Content-Preview**: Vorschau der generierten Inhalte
- **Statistiken**: Anzahl inkludierter Seiten, Content-Länge, letzte Updates
- **Manual Trigger**: Manuelle Regenerierung
- **Validation**: Prüfung der generierten llms.txt auf Korrektheit

#### Automatisierung
- **Scheduler-Task**: Automatische Regenerierung via TYPO3-Scheduler
- **Hooks**: Automatische Aktualisierung bei Seitenänderungen
- **Cache-Integration**: Effiziente Regenerierung nur bei relevanten Änderungen

### Beispiel-Output

```markdown
# Meine TYPO3 Website
> Professionelle Webentwicklung und digitale Lösungen seit 2010

## Über uns
Wir sind ein erfahrenes Team von Webentwicklern und Designern...

## Zentrale Seiten
- [Leistungen](https://example.com/leistungen) - Unsere Kernkompetenzen
- [Projekte](https://example.com/projekte) - Referenzen und Case Studies  
- [Kontakt](https://example.com/kontakt) - Kontaktmöglichkeiten
- [Blog](https://example.com/blog) - Aktuelle Artikel und Insights

## Wichtige Ressourcen
- [FAQ](https://example.com/faq) - Häufig gestellte Fragen
- [Download-Bereich](https://example.com/downloads) - Wichtige Dokumente
```

### Installation und Konfiguration

#### Composer Installation
```bash
composer require vendor/llms-txt-generator
```

#### TypoScript-Setup
```typoscript
plugin.tx_llmstxtgenerator {
    settings {
        siteTitle = {$site.title}
        siteDescription = {$site.description}
        includedPageTypes = 1,4
        maxContentLength = 10000
        outputFormat = markdown
    }
}
```

### CLI-Commands
```bash
# Manuelle Generierung
./vendor/bin/typo3 llms:generate

# Für spezifische Site
./vendor/bin/typo3 llms:generate --site=1

# Full-Version generieren
./vendor/bin/typo3 llms:generate --full
```

### Erweiterte Features

#### SEO-Integration
- Integration mit bestehenden SEO-Extensions (z.B. seo)
- Berücksichtigung von Meta-Tags und strukturierten Daten
- Canonical-URL-Behandlung

#### Performance-Optimierungen
- Caching der generierten llms.txt
- Incremental Updates nur bei Änderungen
- Lazy Loading für große Websites

#### Monitoring und Analytics
- Logging von Generierungsprozessen
- Fehlerbehandlung und Reporting
- Integration mit TYPO3-Logging-Framework

### Extension-Struktur

```
llms_txt_generator/
├── Classes/
│   ├── Controller/
│   │   └── LlmsBackendController.php
│   ├── Service/
│   │   ├── ContentExtractor.php
│   │   ├── LlmsGenerator.php
│   │   └── SiteConfigurationService.php
│   ├── Task/
│   │   └── GenerateLlmsTask.php
│   └── ViewHelpers/
├── Configuration/
│   ├── Backend/
│   ├── TCA/
│   └── TypoScript/
├── Resources/
│   ├── Private/
│   │   ├── Templates/
│   │   ├── Partials/
│   │   └── Layouts/
│   └── Public/
└── ext_emconf.php
```

## Vorteile für TYPO3-Nutzer

- **Automatisierung**: Keine manuelle Pflege der llms.txt erforderlich
- **TYPO3-Integration**: Nahtlose Integration in bestehende Workflows
- **Mehrsprachigkeit**: Automatische Unterstützung mehrsprachiger Sites
- **Flexibilität**: Anpassbare Templates und Filteroptionen
- **Performance**: Effiziente Generierung auch für große Websites
- **Zukunftssicher**: Vorbereitung auf KI-gestützte Websuche

Diese Extension würde TYPO3-Betreibern helfen, ihre Websites optimal für die Nutzung durch Large Language Models zu optimieren, ohne manuellen Aufwand bei der Pflege der llms.txt-Datei.