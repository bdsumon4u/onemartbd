Testing safety and recommended workflow

- Never run tests against your production database.
- This project is configured to use an in-memory SQLite database for PHPUnit via `phpunit.xml` and `.env.testing`.

Run tests safely:

```bash
php artisan test --filter YourTestName
```

Or run a single test file:

```bash
php artisan test --filter ClassName::testMethod
```

Backups:

- Take regular database backups before running wide test suites or destructive artisan commands.
- Use `mysqldump` or your hosting provider tools to snapshot production data.

If you need me to configure a disk-based test DB file instead of `:memory:`, say so and I will switch `.env.testing`.
