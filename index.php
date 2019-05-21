<?php 

require_once 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "[Storage Connection String]";
    
$containerName = "[Blob name]";

$blobClient = BlobRestProxy::createBlobService($connectionString);
    
if (isset($_POST['submit'])) {
	$myfile = strtolower($_FILES["myfile"]["name"]);
	$content = fopen($_FILES["myfile"]["tmp_name"], "r");	

	$blobClient->createBlockBlob($containerName, $myfile, $content);
	header("Location: index.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");

$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Submission 2 Analyze Image</title>
</head>
<body>
	<h1>Upload Gambar</h1>
	Tekan tombol <strong>Choose File</strong> kemudian tekan tombol <strong>Upload</strong> untuk mengupload gambar.
	<br><br>
	<form action="index.php" method="post" enctype="multipart/form-data">
		<input type="file" name="myfile" accept=".jpeg,.jpg,.png" required="">	
		<input type="submit"  name="submit" value="Upload">
	</form>	
	<br>
	<h3>Total Files: <?php echo sizeof($result->getBlobs()) ?></h3>
	<table border="3">
		<thead>
			<tr>
				<th>Nama File</th>
				<th>Url File</th>
				<th></th>
			</tr>
		</thead>
		<tbody>			
			<?php 
				do{					    
				    foreach ($result->getBlobs() as $oneblob) {					    						   
			?>
				<tr>
					<td><?php echo $oneblob->getName() ?></td> 
					<td><?php echo $oneblob->getUrl() ?></td> 
					<td>
						<form action="cognitive_services.php" method="post">
							<input type="hidden" name="url" value="<?php echo $oneblob->getUrl()?>">
							<input type="submit" name="submit" value="Analyze">
						</form>
					</td> 
				</tr>				
			<?php 
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
			?>			
		</tbody>		
	</table>
</body>
</html>
