<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addPayrollColumnsToStaffTables();
        $this->addPolymorphicColumns();
        $this->backfillGuardOwnedData();
        $this->relaxLegacyUserColumns();
    }

    public function down(): void
    {
        Schema::table('monthly_payrolls', function (Blueprint $table): void {
            if (Schema::hasColumn('monthly_payrolls', 'staff_type')) {
                $table->dropUnique('monthly_payrolls_staff_type_staff_id_month_year_unique');
                $table->dropIndex('monthly_payrolls_staff_type_staff_id_index');
                $table->dropColumn(['staff_type', 'staff_id']);
            }

            if (Schema::hasColumn('monthly_payrolls', 'generated_by_type')) {
                $table->dropIndex('monthly_payrolls_generated_by_type_generated_by_id_index');
                $table->dropColumn(['generated_by_type', 'generated_by_id']);
            }
        });

        Schema::table('user_bonuses', function (Blueprint $table): void {
            if (Schema::hasColumn('user_bonuses', 'staff_type')) {
                $table->dropIndex('user_bonuses_staff_type_staff_id_index');
                $table->dropColumn(['staff_type', 'staff_id']);
            }
        });

        Schema::table('salary_advances', function (Blueprint $table): void {
            if (Schema::hasColumn('salary_advances', 'staff_type')) {
                $table->dropIndex('salary_advances_staff_type_staff_id_index');
                $table->dropColumn(['staff_type', 'staff_id']);
            }

            if (Schema::hasColumn('salary_advances', 'approved_by_type')) {
                $table->dropIndex('salary_advances_approved_by_type_approved_by_id_index');
                $table->dropColumn(['approved_by_type', 'approved_by_id']);
            }
        });

        Schema::table('attendances', function (Blueprint $table): void {
            if (Schema::hasColumn('attendances', 'staff_type')) {
                $table->dropUnique('attendances_staff_type_staff_id_date_unique');
                $table->dropIndex('attendances_staff_type_staff_id_index');
                $table->dropColumn(['staff_type', 'staff_id']);
            }
        });

        foreach (['admins', 'managers', 'employees'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $columns = ['panel_start', 'panel_end', 'order_start', 'order_end', 'monthly_salary', 'off_days'];

                foreach ($columns as $column) {
                    if (Schema::hasColumn($table->getTable(), $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    private function addPayrollColumnsToStaffTables(): void
    {
        foreach (['admins', 'managers', 'employees'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'panel_start')) {
                    $table->time('panel_start')->nullable()->after('end_time');
                }

                if (! Schema::hasColumn($tableName, 'panel_end')) {
                    $table->time('panel_end')->nullable()->after('panel_start');
                }

                if (! Schema::hasColumn($tableName, 'order_start')) {
                    $table->time('order_start')->nullable()->after('panel_end');
                }

                if (! Schema::hasColumn($tableName, 'order_end')) {
                    $table->time('order_end')->nullable()->after('order_start');
                }

                if (! Schema::hasColumn($tableName, 'monthly_salary')) {
                    $table->decimal('monthly_salary', 10, 2)->default(0)->after('order_end');
                }

                if (! Schema::hasColumn($tableName, 'off_days')) {
                    $table->text('off_days')->nullable()->after('monthly_salary');
                }
            });
        }
    }

    private function addPolymorphicColumns(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            if (! Schema::hasColumn('attendances', 'staff_type')) {
                $table->string('staff_type')->nullable()->after('id');
            }

            if (! Schema::hasColumn('attendances', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('staff_type');
            }

            if (! $this->hasIndex('attendances', 'attendances_staff_type_staff_id_index')) {
                $table->index(['staff_type', 'staff_id']);
            }

            if (! $this->hasIndex('attendances', 'attendances_staff_type_staff_id_date_unique')) {
                $table->unique(['staff_type', 'staff_id', 'date']);
            }
        });

        Schema::table('salary_advances', function (Blueprint $table): void {
            if (! Schema::hasColumn('salary_advances', 'staff_type')) {
                $table->string('staff_type')->nullable()->after('id');
            }

            if (! Schema::hasColumn('salary_advances', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('staff_type');
            }

            if (! Schema::hasColumn('salary_advances', 'approved_by_type')) {
                $table->string('approved_by_type')->nullable()->after('note');
            }

            if (! Schema::hasColumn('salary_advances', 'approved_by_id')) {
                $table->unsignedBigInteger('approved_by_id')->nullable()->after('approved_by_type');
            }

            if (! $this->hasIndex('salary_advances', 'salary_advances_staff_type_staff_id_index')) {
                $table->index(['staff_type', 'staff_id']);
            }

            if (! $this->hasIndex('salary_advances', 'salary_advances_approved_by_type_approved_by_id_index')) {
                $table->index(['approved_by_type', 'approved_by_id']);
            }
        });

        Schema::table('user_bonuses', function (Blueprint $table): void {
            if (! Schema::hasColumn('user_bonuses', 'staff_type')) {
                $table->string('staff_type')->nullable()->after('id');
            }

            if (! Schema::hasColumn('user_bonuses', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('staff_type');
            }

            if (! $this->hasIndex('user_bonuses', 'user_bonuses_staff_type_staff_id_index')) {
                $table->index(['staff_type', 'staff_id']);
            }
        });

        Schema::table('monthly_payrolls', function (Blueprint $table): void {
            if (! Schema::hasColumn('monthly_payrolls', 'staff_type')) {
                $table->string('staff_type')->nullable()->after('id');
            }

            if (! Schema::hasColumn('monthly_payrolls', 'staff_id')) {
                $table->unsignedBigInteger('staff_id')->nullable()->after('staff_type');
            }

            if (! Schema::hasColumn('monthly_payrolls', 'generated_by_type')) {
                $table->string('generated_by_type')->nullable()->after('status');
            }

            if (! Schema::hasColumn('monthly_payrolls', 'generated_by_id')) {
                $table->unsignedBigInteger('generated_by_id')->nullable()->after('generated_by_type');
            }

            if (! $this->hasIndex('monthly_payrolls', 'monthly_payrolls_staff_type_staff_id_index')) {
                $table->index(['staff_type', 'staff_id']);
            }

            if (! $this->hasIndex('monthly_payrolls', 'monthly_payrolls_staff_type_staff_id_month_year_unique')) {
                $table->unique(['staff_type', 'staff_id', 'month', 'year']);
            }

            if (! $this->hasIndex('monthly_payrolls', 'monthly_payrolls_generated_by_type_generated_by_id_index')) {
                $table->index(['generated_by_type', 'generated_by_id']);
            }
        });
    }

    private function backfillGuardOwnedData(): void
    {
        $usersQuery = DB::table('users');
        $userColumns = $this->existingColumns('users', [
            'id',
            'name',
            'email',
            'phone',
            'start_time',
            'end_time',
            'panel_start',
            'panel_end',
            'order_start',
            'order_end',
            'monthly_salary',
            'off_days',
        ]);

        if (Schema::hasColumn('users', 'role')) {
            $usersQuery->whereIn('role', [1, 2, 3]);
            $userColumns[] = 'role';
        }

        $users = $usersQuery->get($userColumns);

        $userToStaffMap = [];

        foreach ($users as $user) {
            $resolved = $this->resolveStaffByLegacyUser($user);

            if (! $resolved) {
                continue;
            }

            [$staffType, $staffTable, $staffId] = $resolved;
            $userToStaffMap[(int) $user->id] = [$staffType, (int) $staffId];

            DB::table($staffTable)
                ->where('id', $staffId)
                ->update([
                    'panel_start' => $user->panel_start ?? null,
                    'panel_end' => $user->panel_end ?? null,
                    'order_start' => $user->order_start ?? null,
                    'order_end' => $user->order_end ?? null,
                    'monthly_salary' => $user->monthly_salary ?? 0,
                    'off_days' => $user->off_days ?? null,
                    'start_time' => $user->start_time ?? null,
                    'end_time' => $user->end_time ?? null,
                ]);
        }

        foreach (DB::table('attendances')->get(['id', 'user_id']) as $row) {
            if (! $row->user_id || ! isset($userToStaffMap[(int) $row->user_id])) {
                continue;
            }

            [$staffType, $staffId] = $userToStaffMap[(int) $row->user_id];

            DB::table('attendances')->where('id', $row->id)->update([
                'staff_type' => $staffType,
                'staff_id' => $staffId,
            ]);
        }

        foreach (DB::table('salary_advances')->get(['id', 'user_id', 'approved_by']) as $row) {
            $update = [];

            if ($row->user_id && isset($userToStaffMap[(int) $row->user_id])) {
                [$staffType, $staffId] = $userToStaffMap[(int) $row->user_id];
                $update['staff_type'] = $staffType;
                $update['staff_id'] = $staffId;
            }

            if ($row->approved_by && isset($userToStaffMap[(int) $row->approved_by])) {
                [$approvedByType, $approvedById] = $userToStaffMap[(int) $row->approved_by];
                $update['approved_by_type'] = $approvedByType;
                $update['approved_by_id'] = $approvedById;
            }

            if ($update !== []) {
                DB::table('salary_advances')->where('id', $row->id)->update($update);
            }
        }

        foreach (DB::table('user_bonuses')->get(['id', 'user_id']) as $row) {
            if (! $row->user_id || ! isset($userToStaffMap[(int) $row->user_id])) {
                continue;
            }

            [$staffType, $staffId] = $userToStaffMap[(int) $row->user_id];

            DB::table('user_bonuses')->where('id', $row->id)->update([
                'staff_type' => $staffType,
                'staff_id' => $staffId,
            ]);
        }

        foreach (DB::table('monthly_payrolls')->get(['id', 'user_id', 'generated_by']) as $row) {
            $update = [];

            if ($row->user_id && isset($userToStaffMap[(int) $row->user_id])) {
                [$staffType, $staffId] = $userToStaffMap[(int) $row->user_id];
                $update['staff_type'] = $staffType;
                $update['staff_id'] = $staffId;
            }

            if ($row->generated_by && isset($userToStaffMap[(int) $row->generated_by])) {
                [$generatedByType, $generatedById] = $userToStaffMap[(int) $row->generated_by];
                $update['generated_by_type'] = $generatedByType;
                $update['generated_by_id'] = $generatedById;
            }

            if ($update !== []) {
                DB::table('monthly_payrolls')->where('id', $row->id)->update($update);
            }
        }
    }

    private function relaxLegacyUserColumns(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            if (Schema::hasColumn('attendances', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
        });

        Schema::table('salary_advances', function (Blueprint $table): void {
            if (Schema::hasColumn('salary_advances', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }

            if (Schema::hasColumn('salary_advances', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->change();
            }
        });

        Schema::table('user_bonuses', function (Blueprint $table): void {
            if (Schema::hasColumn('user_bonuses', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
        });

        Schema::table('monthly_payrolls', function (Blueprint $table): void {
            if (Schema::hasColumn('monthly_payrolls', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }

            if (Schema::hasColumn('monthly_payrolls', 'generated_by')) {
                $table->unsignedBigInteger('generated_by')->nullable()->change();
            }
        });
    }

    /**
     * @return array{0:string,1:string,2:int}|null
     */
    private function resolveStaffByLegacyUser(object $user): ?array
    {
        $matches = [];

        foreach ($this->candidateStaffMappings($user) as [$staffType, $staffTable]) {
            $query = DB::table($staffTable);

            if (! empty($user->email) && Schema::hasColumn($staffTable, 'email')) {
                $query->where('email', $user->email);
            } elseif (! empty($user->phone) && Schema::hasColumn($staffTable, 'phone')) {
                $query->where('phone', $user->phone);
            } else {
                $query->where('name', $user->name);
            }

            $staffId = $query->value('id');

            if ($staffId) {
                $matches[] = [$staffType, $staffTable, (int) $staffId];
            }
        }

        if (count($matches) !== 1) {
            return null;
        }

        return $matches[0];
    }

    /**
     * @return list<array{0:string,1:string}>
     */
    private function candidateStaffMappings(object $user): array
    {
        if (isset($user->role)) {
            return match ((int) $user->role) {
                1 => [['admin', 'admins']],
                2 => [['manager', 'managers']],
                3 => [['employee', 'employees']],
                default => [],
            };
        }

        return [
            ['admin', 'admins'],
            ['manager', 'managers'],
            ['employee', 'employees'],
        ];
    }

    private function hasIndex(string $tableName, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->whereRaw('table_schema = schema()')
            ->where('table_name', $tableName)
            ->where('index_name', $indexName)
            ->exists();
    }

    /**
     * @param  list<string>  $columns
     * @return list<string>
     */
    private function existingColumns(string $tableName, array $columns): array
    {
        return array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn($tableName, $column)
        ));
    }
};
