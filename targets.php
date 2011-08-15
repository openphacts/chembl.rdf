<?php header('Content-type: application/rdf+xml'); ?>

<?php

include 'vars.php';
include 'namespaces.php';
include 'functions.php';

mysql_connect("localhost", $user, $pwd) or die(mysql_error());
# echo "<!-- Connection to the server was successful! -->\n";

mysql_select_db($db) or die(mysql_error());
# echo "<!-- Database was selected! -->\n";

$allIDs = mysql_query("SELECT DISTINCT * FROM target_dictionary" . $limit);

$num = mysql_numrows($allIDs);

while ($row = mysql_fetch_assoc($allIDs)) {
  $target = $TRG . "t" . $row['tid'];
  echo triple( $target, $RDF . "type", $ONTO . "Target" );
  echo triple( $target, $ONTO . "hasTargetType", $TGT . $row['target_type'] );
  if ($row['organism'])
    echo dataTriple( $target, $ONTO . "organism", $row['organism'] );
  if ($row['description'])
    echo dataTriple( $target, $ONTO . "hasDescription",  str_replace("\"", "\\\"", $row['description']) ); 
  if ($row['synonyms']) {
    $synonyms = preg_split("/[;]+/", $row['synonyms']);
    foreach ($synonyms as $i => $synonym) {
      echo dataTriple( $target, $RDFS . "label", str_replace("\"", "\\\"", trim($synonym)) );
    }
  }
  if ($row['keywords']) {
    $keywords = preg_split("/[;]+/", $row['keywords']);
    foreach ($keywords as $i => $keyword) {
      echo dataTriple( $target, $ONTO . "hasKeyword", str_replace("\"", "\\\"", trim($keyword)) );
    }
  }
  if ($row['protein_sequence'])
    echo dataTriple( $target, $ONTO . "sequence", $row['protein_sequence'] );
  if ($row['ec_number']) {
    echo dataTriple( $target, $DC . "identifier", $row['ec_number'] );
    echo triple( $target, $OWL . "sameAs", "http://bio2rdf.org/ec:" . $row['ec_number'] );
  }
  if ($row['protein_accession'])
    echo dataTriple( $target, $DC . "identifier",  "uniprot:" . $row['protein_accession'] );
    echo triple( $target, $OWL . "sameAs", "http://bio2rdf.org/uniprot:" . $row['protein_accession'] );
  if ($row['tax_id'])
    echo triple( $target, $ONTO . "hasTaxonomy", "http://bio2rdf.org/taxonomy:" . $row['tax_id'] );

  # classifications
  $class = mysql_query("SELECT DISTINCT * FROM target_class WHERE tid = \"" . $row['tid'] . "\"");
  if ($classRow = mysql_fetch_assoc($class)) {
    if ($classRow['l1']) echo dataTriple( $target, $ONTO . "classL1", str_replace("\"", "\\\"", $classRow['l1']) );
    if ($classRow['l2']) echo dataTriple( $target, $ONTO . "classL2", str_replace("\"", "\\\"", $classRow['l2']) );
    if ($classRow['l3']) echo dataTriple( $target, $ONTO . "classL3", str_replace("\"", "\\\"", $classRow['l3']) );
    if ($classRow['l4']) echo dataTriple( $target, $ONTO . "classL4", str_replace("\"", "\\\"", $classRow['l4']) );
    if ($classRow['l5']) echo dataTriple( $target, $ONTO . "classL5", str_replace("\"", "\\\"", $classRow['l5']) );
    if ($classRow['l6']) echo dataTriple( $target, $ONTO . "classL6", str_replace("\"", "\\\"", $classRow['l6']) );
    if ($classRow['l7']) echo dataTriple( $target, $ONTO . "classL7", str_replace("\"", "\\\"", $classRow['l7']) );
    if ($classRow['l8']) echo dataTriple( $target, $ONTO . "classL8", str_replace("\"", "\\\"", $classRow['l8']) );
  }

  echo dataTriple( $target, $DC . "title", $row['pref_name'] );
}

?>
