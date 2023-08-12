<?php

namespace Tests\Feature\base;

use Carbon\Carbon;
use Tests\TestCase;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
        $this->artisan('db:seed');
    }

    public function getCurrentDate(): string
    {
        return Carbon::now()->format('Y-m-d');
    }

    public function getDeadlineAfterToday(): string
    {
        $parameterDate = $this->getCurrentDate();

        $carbonParameterDate = Carbon::createFromFormat('Y-m-d', $parameterDate);

        return $carbonParameterDate->addDay()->format('Y-m-d');
    }

    public function getDeadlineBeforeToday(): string
    {
        $parameterDate = Carbon::now()->format('Y-m-d');

        $carbonParameterDate = Carbon::createFromFormat('Y-m-d', $parameterDate);

        return $carbonParameterDate->subDay()->format('Y-m-d');
    }
}
