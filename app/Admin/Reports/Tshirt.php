<?php

namespace App\Admin\Reports;

use App\Models\Profile;

class Tshirt extends Report
{
    public $name = 'T-Shirt';

    public $filename = 'T-Shirt-Report';

    public $view = 'admin.reports.tshirt';

    public function query()
    {
        return Profile::all();
    }
}
