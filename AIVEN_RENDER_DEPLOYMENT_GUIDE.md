# Aiven MySQL on Render Deployment Guide

## Render Environment Variables

Set these on the Render web service:

```text
APP_ENV=production
APP_BASE_URL=https://your-render-url.onrender.com/
DB_HOST=your-aiven-host.aivencloud.com
DB_PORT=your-aiven-port
DB_NAME=defaultdb
DB_USER=avnadmin
DB_PASS=your-aiven-password
DB_SSL=true
```

`DB_SSL=true` enables CodeIgniter's mysqli SSL connection mode.

## Export Local XAMPP Database

From the XAMPP machine:

```powershell
C:\xampp7\mysql\bin\mysqldump.exe -uroot wmsci > wmsci-aiven-import.sql
```

If the active local MySQL install is under `C:\xampp`, use:

```powershell
C:\xampp\mysql\bin\mysqldump.exe -uroot wmsci > wmsci-aiven-import.sql
```

## Import Into Aiven

Use the Aiven MySQL connection values from the Aiven console. Import into `defaultdb`:

```powershell
mysql --host=your-aiven-host.aivencloud.com --port=your-aiven-port --user=avnadmin --password --ssl-mode=REQUIRED defaultdb < wmsci-aiven-import.sql
```

When prompted, paste the Aiven password. Do not save the password in project files.

## Render Docker

Render should use:

```text
Dockerfile path: ./Dockerfile
```

After deployment, test:

- `/index.php/login`
- `/index.php/admin`
- sales and invoice pages
- existing stock transactions
- PDF downloads

## Notes

- Local XAMPP still falls back to `localhost`, database `wmsci`, user `root`, and an empty password when DB environment variables are not set.
- Aiven should use `DB_NAME=defaultdb` unless you created a different database in Aiven.
