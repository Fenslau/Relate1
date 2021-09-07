<?php
namespace App\MyClasses;

use App\Models\Progress;

class GetProgress {

  public function __construct($vkid, $process, $info, $count_ALL, $count_1) {

	$this->progress = new Progress();
	if ($vkid) $this->vkid = $vkid; else $this->vkid = 1000;
	$this->process = $process;
    $data = array();
    $data['width'] = $this->width = 0;
    $data['info'] = $this->info = $info;
    $this->count_step = intdiv ($count_ALL, $count_1);
    $this->progress->updateOrCreate(['vkid' => $this->vkid, 'process' => $this->process], $data);
  }

  public function step () {

    if ($this->count_step) $this->width += round((100/$this->count_step),3); else $this->width = 100;
    if ($this->width > 100) $this->width = 100;
    $data = array();
    $data['info'] = $this->info;
    $data['width'] = $this->width;

    $this->progress->updateOrCreate(['vkid' => $this->vkid, 'process' => $this->process], $data);
  }

  public function __destruct() {
	   $this->progress->where(['vkid' => $this->vkid, 'process' => $this->process])->delete();
  }
}
?>
