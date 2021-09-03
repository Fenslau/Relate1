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
      $this->tarif=$this->tarif();
    }

  public function tarif() {
      $oplata = new Oplata();
      $tarif = $oplata->where('vkid', $this->vkid)->first();
      if (isset($tarif->date) AND $tarif->date > date('U'))
      $tarif=$tarif;
      else return FALSE;
      return $tarif;
    }
  }

 ?>
