{
"name": "makuruwan/export-project-structure",
"description": "A Laravel package to export project structure files into text files.",
"type": "library",
"license": "MIT",
"keywords": [
"laravel",
"artisan",
"export",
"project-structure"
],
"autoload": {
"psr-4": {
"Makuruwan\\ExportProjectStructure\\": "src/"
}
},
"extra": {
"laravel": {
"providers": [
"Makuruwan\\ExportProjectStructure\\ExportProjectStructureServiceProvider"
]
}
},
"require": {
"php": "^8.1",
"illuminate/support": "^10.0|^11.0|^12.0"
},
"minimum-stability": "stable",
"prefer-stable": true
}