This folder contains a small export of the project's key files and their descriptions.

files:
- file_catalog.csv — CSV you can import into Google Sheets or Excel. Columns: File, Path, Short Description, Functionality Summary

How to open in Google Sheets:
1. Open Google Sheets.
2. File → Import → Upload → Select `file_catalog.csv` from this repository (download locally first), or use "Upload".
3. Choose "Replace spreadsheet" or "Insert new sheet" and finish the import.

How to open locally (PowerShell):
```powershell
# From repository root
Start-Process .\exports\file_catalog.csv
```

If you'd like, I can:
- Expand the CSV with more files and routes.
- Add a small script that generates this export automatically from your codebase (parses controllers/models).
- Create a Google Sheets API script to push the CSV into a specific Google Sheet (requires credentials).

Generated on: 2025-10-07
