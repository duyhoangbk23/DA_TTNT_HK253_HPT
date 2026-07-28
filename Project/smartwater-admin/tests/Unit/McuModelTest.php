<?php

namespace Tests\Unit;

use App\Models\Mcu;
use Tests\TestCase;

class McuModelTest extends TestCase
{
    public function test_serial_number_must_follow_sn_six_digits_format(): void
    {
        $this->assertTrue(Mcu::isValidSerialNumber('SN-123456'));
        $this->assertFalse(Mcu::isValidSerialNumber('SN-12345'));
        $this->assertFalse(Mcu::isValidSerialNumber('ABC123456'));
    }

    public function test_display_status_returns_na_for_empty_status(): void
    {
        $this->assertSame('N/A', Mcu::getDisplayStatus(null));
        $this->assertSame('N/A', Mcu::getDisplayStatus(''));
        $this->assertSame('Online', Mcu::getDisplayStatus('online'));
    }

    public function test_devices_relation_uses_string_mcu_id(): void
    {
        $relation = (new Mcu())->devices();

        $this->assertSame('mcu_id', $relation->getForeignKeyName());
        $this->assertSame('mcu_id', $relation->getLocalKeyName());
    }
}
