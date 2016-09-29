<?php

/* Copy inc_sample.php to inc.php and fill in your credentials */

include_once("inc.php");

function getAutoren() {
  $query="SELECT * FROM pvm_pvmkit_authors";
  $res=selectQuery($query); 
  $out='';
  foreach ($res as $row) {
    $out.=getPerson($row);
  }
  return display(TurtlePrefixAutoren().$out);
}

function getPerson($arow) {
  $aid=$arow['author_id'];
  $uid=$arow['user_id'];
  $name=$arow['full_name'];
  $a=array();
  $a[]=' a foaf:Person ';
  $a[]=' pvm:hasAID "'.$aid.'"';
  $a[]=' foaf:name "'.$name.'"';
  if (!empty($uid)) { $a[]=' pvm:associatedUser pvmp:U'.$uid ; }
  return 'pvmp:A'. $aid . join(" ;\n  ",$a) . " . \n\n" ;
}

function OldgetPerson($arow) {
  $aid=$arow['author_id'];
  $uid=$arow['user_id'];
  $name=$arow['full_name'];
  $a=array();
  $a[]=' a foaf:Person ';
  $a[]=' pvm:hasAID "'.$aid.'"';
  $a[]=' foaf:name "'.$name.'"';
  if (!empty($uid)) {
    $query="SELECT * FROM pvm_users where ID='$uid'";
    $res = selectQuery($query);
    while ($row = $res->fetch_assoc()) {
      $a[]=' pvm:hasUID "'.$row['ID'].'"';
      $a[]=' foaf:accountName "'.$row['display_name'].'"';
      $a[]=' foaf:nick "'.$row['user_nicename'].'"';
      $a[]=' foaf:mbox "'.$row['user_email'].'"';
      $a[]=' dcterms:created "'
	.str_replace(" ", "T", $row['user_registered']).'"';
    }
  }
  return 'pvmp:P'. $aid . join(" ;\n  ",$a) . " . \n\n" ;
}

function TurtlePrefixAutoren() {
return '
@prefix pvm: <http://pvm.uni-leipzig.de/Model/> .
@prefix pvmp: <http://pvm.uni-leipzig.de/Data/Person/> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix foaf: <http://xmlns.com/foaf/0.1/> .
@prefix dcterms: <http://purl.org/dc/terms/> .

<http://pvm.uni-leipzig.de/Data/Personen/>
    a owl:Ontology ;
    rdfs:label "Dump aus der PVM-Produktivinstanz" .

';
}

if (defined( 'ABSPATH' )) {
  // define WP shortcode
  add_shortcode( 'getAutoren', 'getAutoren' );
} else { echo getAutoren(); }

?>
