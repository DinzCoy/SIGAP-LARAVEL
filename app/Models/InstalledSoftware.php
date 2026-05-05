<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstalledSoftware extends Model
{
    protected $fillable = [
        'pc_report_id',
        'software_name',
        'software_version',
        'software_publisher',
    ];

    public function pcReport()
    {
        return $this->belongsTo(PcReport::class);
    }

    //Menentukan apakah software merupakan antivirus/security.
    public function getIsAntivirusAttribute()
    {
        $keywords = ['antivirus', 'defender', 'security', 'bitdefender', 'smadav', 'kaspersky', 'mcafee', 'avast', 'eset', 'norton', 'malware', 'trend micro', 'sophos', 'endpoint'];
        $name = strtolower($this->software_name);
        
        foreach ($keywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
}
