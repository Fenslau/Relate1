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
      $this->tarif();
    }

  public function tarif() {
      $oplata = new Oplata();
      $tarif = $oplata->where('vkid', $this->vkid)->orderBy('id', 'desc')->first();
      if (isset($tarif->date) AND strtotime($tarif->date) > date('U')) {
        $this->demo = $tarif->demo;
        $this->date = $tarif->date;
      } else {

        $this->demo = NULL;
        $this->date = NULL;


      }
  }
}
?>
