<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('xuxemons:recompensas-diarias')->dailyAt('08:00');