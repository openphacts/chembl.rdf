<?php header('Content-type: application/rdf+xml');
print("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
?>
<!DOCTYPE rdf:RDF [
  <!ENTITY gt "&#62;">
  <!ENTITY lt "&#60;">
  <!ENTITY ch "http://pele.farmbio.uu.se/chembl/?">
  <!ENTITY bodo "http://www.blueobelisk.org/ontologies/chemoinformatics-algorithms/#">
]>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
         xmlns:nmr="http://www.nmrshiftdb.org/onto#"
         xmlns:chembl="&ch;"
         xmlns:chem="http://www.blueobelisk.org/chemistryblogs/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:foaf="http://xmlns.com/foaf/0.1/"
         xmlns:bodo="&bodo;"
         xmlns:owl="http://www.w3.org/2002/07/owl#"
         xmlns:bibo="http://purl.org/ontology/bibo/">

<?php 

include 'vars.php';

$ns = "&ch;";

#$limit = " LIMIT 5";
$limit = "";

mysql_connect("localhost", $user, $pwd) or die(mysql_error());
# echo "<!-- Connection to the server was successful! -->\n";

mysql_select_db("chembl_02") or die(mysql_error());
# echo "<!-- Database was selected! -->\n";

$allIDs = mysql_query("SELECT DISTINCT journal FROM docs" . $limit);

$num = mysql_numrows($allIDs);

while ($row = mysql_fetch_assoc($allIDs)) {
  echo "<rdf:Description rdf:about=\"" . $ns . "journalId=" . md5($row['journal']) . "\">\n";
  echo "  <rdf:type rdf:resource=\"http://purl.org/ontology/bibo/Journal\" />\n";
  echo "  <dc:title>" . $row['journal'] . "</dc:title>\n";
  echo "</rdf:Description>\n";
}

$allIDs = mysql_query("SELECT DISTINCT * FROM docs" . $limit);

$num = mysql_numrows($allIDs);

while ($row = mysql_fetch_assoc($allIDs)) {
  echo "<rdf:Description rdf:about=\"" . $ns . "documentId=" . $row['doc_id'] . "\">\n";
  echo "  <rdf:type rdf:resource=\"http://purl.org/ontology/bibo/Article\" />\n";
  if ($row['doi']) echo "  <bibo:doi>" . $row['doi'] . "</bibo:doi>\n";
  if ($row['pubmed_id']) echo "  <bibo:pmid>" . $row['pubmed_id'] . "</bibo:pmid>\n";
  echo "  <dc:date>" . $row['year'] . "</dc:date>\n";
  echo "  <bibo:volume>" . $row['volume'] . "</bibo:volume>\n";
  echo "  <bibo:issue>" . $row['issue'] . "</bibo:issue>\n";
  echo "  <bibo:pageStart>" . $row['first_page'] . "</bibo:pageStart>\n";
  echo "  <bibo:pageEnd>" . $row['last_page'] . "</bibo:pageEnd>\n";
  echo "  <dc:isPartOf rdf:resource=\"" . $ns . "journalId=" . md5($row['journal']) . "\" />\n";
  echo "</rdf:Description>\n";
}

?>

</rdf:RDF>