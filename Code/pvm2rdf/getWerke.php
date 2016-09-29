<?php

/* Copy inc_sample.php to inc.php and fill in your credentials */

include_once("inc.php");

function getWerke() {
  $query="SELECT * FROM pvm_pvmkit_packages where status=8";
  $res=selectQuery($query); 
  $out='';
  foreach ($res as $row) {
    $out.=getWerk($row);
  }
  return display(TurtlePrefixWerke().$out);
}

function getObjects($id) { // Objekte zum Package $id finden
  $query="SELECT id,a.type,author,content FROM pvm_pvmkit_package_components a, pvm_pvmkit_objects where package_id=$id and object_id=id";
  $res=selectQuery($query); 
  $a=array();
  foreach ($res as $row) {
    $a[$row['id']]=getObject($row);
  }
  return $a;
}

function getObject($a) {
  $id=$a['id'];
  $type=$a['type']; 
  $author=$a['author'];
  $content=$a['content'];
  $a=array();
  $a[]=' a pvm:Objekt ';
  $a[]=' pvm:hasID "'.$id.'"';
  $a[]=' pvm:hasType "'.$type.'"';
  $a[]=' pvm:hasContent """'.$content.'"""';
  return "pvmo:O$id" . join(" ;\n  ",$a) . " . \n" ; 
}

function getProperties($id) { // Properties zum Package $id finden
  $query="SELECT * FROM pvm_pvmkit_property_index where package_id=$id";
  $res=selectQuery($query); 
  $a=array();
  foreach ($res as $row) {
    $a[]="pvmprop:P".$row['property_id'];
  }
  return join(", ",$a);
}

function getWerk($arow) {
  $id=$arow['id'];
  $b=array();
  $c=array();
  foreach(getObjects($id) as $key => $value) {
    $b[]="pvmo:O".$key;
    $c[]=$value;
  }
  $objects=join(", ",$b); 
  $properties=getProperties($id); 
  $a=array();
  $a[]=' a pvm:Werk ';
  $a[]=' pvm:hasID "'.$id.'"';
  $a[]=' rdfs:label "'.$arow['title'].'"';
  $a[]=' dcterms:creator pvmp:A'.$arow['author'];
  $a[]=' dcterms:created "'
	.str_replace(" ", "T", $arow['publish_date']).'"';
  if (!empty($objects)) { $a[]=' pvm:hasObjects '.$objects ; }
  if (!empty($properties)) { $a[]=' pvm:hasProperties '.$properties ; }
  return "pvmw:W$id" . join(" ;\n  ",$a) . " . \n" 
    . join("",$c) . " \n" ;
}

function TurtlePrefixWerke() {
return '
@prefix pvm: <http://pvm.uni-leipzig.de/Model/> .
@prefix pvmp: <http://pvm.uni-leipzig.de/Data/Person/> .
@prefix pvmprop: <http://pvm.uni-leipzig.de/Data/Property/> .
@prefix pvmw: <http://pvm.uni-leipzig.de/Data/Werk/> .
@prefix pvmo: <http://pvm.uni-leipzig.de/Data/Objekt/> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix dcterms: <http://purl.org/dc/terms/> .

<http://pvm.uni-leipzig.de/Data/Werke/>
    a owl:Ontology ;
    rdfs:label "Dump aus der PVM Werke-Datenbank. Nur die veröffentlichten werke" .

';
}

if (defined( 'ABSPATH' )) {
  // define WP shortcode
  add_shortcode( 'getWerke', 'getWerke' );
} else { echo getWerke(); }

?>
