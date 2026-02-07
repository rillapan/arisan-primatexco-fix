<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\MonthlyPeriod;
use Illuminate\Support\Str;

class CreateMonthlyPeriod extends Command
{
    protected $signature = 'create:period';
    protected $description = 'Create a monthly period for testing';

    public function handle()
    {
        $group = Group::first();
        
        if (!$group) {
            $this->error('No group found');
            return 1;
        }

        $period = MonthlyPeriod::create([
            'group_id' => $group->id,
            'period_name' => 'Periode 2026',
            'period_start' => '2026-01-01',
            'period_end' => '2026-12-31',
            'previous_cash_balance' => 0,
            'total_installments' => $group->participants()->where('is_active', true)->count() * $group->monthly_installment,
            'total_amount' => $group->participants()->where('is_active', true)->count() * $group->monthly_installment,
            'shu_amount' => 0,
            'available_funds' => $group->participants()->where('is_active', true)->count() * $group->monthly_installment,
            'status' => 'active'
        ]);

        $this->info('Created monthly period: ' . $period->id);
        return 0;
    }
}
