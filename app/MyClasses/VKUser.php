<?php
namespace App\MyClasses;
use App\Models\Oplata;
use App\Models\Top;
use App\Models\Visitors;
use App\Models\Download;

class VKUser {

    public $vkid;

    public function __construct($vkid) {
      $this->vkid = $vkid;
      $oplata = new Oplata();
      $tarif = $oplata->where('vkid', $this->vkid)->orderBy('id', 'desc')->first();
      if (isset($tarif->date)) {
        $this->demo = $tarif->demo;
        $this->date = $tarif->date;
        $this->old_post_limit = $tarif->old_post_limit;
        $this->old_post_fact = $tarif->old_post_fact;
        $this->project_limit = $tarif->project_limit;
        $this->rules_limit = $tarif->rules_limit;
      } else {
        $this->demo = NULL;
        $this->date = NULL;
      }
    }

    public function save() {
      $oplata = new Oplata();
      $tarif = $oplata->where('vkid', $this->vkid)->orderBy('id', 'desc')->first();

      $tarif->old_post_fact = $this->old_post_fact;

      $tarif->demo = $this->demo;
      $tarif->date = $this->date;
      $tarif->old_post_limit = $this->old_post_limit;
      $tarif->project_limit = $this->project_limit;
      $tarif->rules_limit = $this->rules_limit;

      $tarif->save();
    }
}
?>
