<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('refunds:auto-reject')->everyMinute();
Schedule::command('orders:auto-complete')->everyMinute();