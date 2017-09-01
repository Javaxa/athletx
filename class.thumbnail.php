<?php

class Thumbnail {
	public $imageObject     = null;
	public $imagePath       = null;
	public $width           = 64;
	public $height          = 64;
	public $mimeType        = null;
	
	public static $supportedTypes = [
		"image/gif",
		"image/jpeg",
		"image/png",
		"video/mp4",
		"video/quicktime",
		"video/x-m4v",
	];

	public static function supports($contentType) {
		return in_array($contentType, self::$supportedTypes);
	}

	public function getFileMimeType($file)
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $file);
		return $mimeType;
	}

	public function createResourceFromType($type, $src)
	{
		if (!$src) return false;
		
		switch ($type) {
			case "image/gif":
				$image = @imagecreatefromgif($src);
				break;
			case "image/jpeg":
			case "image/jpg":
				$image = @imagecreatefromjpeg($src);
				break;
			case "image/png":
				$image = @imagecreatefrompng($src);
				@imagesavealpha($image, true); // if the thumbnail doesn't get scaled, alpha is lost
				break;
			case "image/bmp":
				$image = @imagecreatefromwbmp($src);
				break;
			default:
				$image = false;
				break;
		}

		return $image;
	}

	public function createResourceFromString($string) {
		return imagecreatefromstring($string);
	}

	public function scaleTo($options)
	{
		$size = $options["scale"];
		$width = $options["width"];
		$height = $options["height"];
		$keepProportion = $options["keepProportion"];
		$transparency = $options["transparency"];

		if ($size === null) $size = 1;
		if ($keepProportion === null) $keepProportion = true;
		if ($transparency === null) $transparency = false;

		if ($this->imageObject) {
			$newWidth  = $this->width * $size;
			$newHeight = $this->height * $size;

			if ($width || $height) {
				if ($width && $height) {
					$newHeight = $height;
					$newWidth = $width;
				}
				else {
					$ratio = $this->width / $this->height;
					if ($width) {
						$newWidth = $width;
						if ($keepProportion) {
							$newHeight = $width / $ratio;
						}
					}
					else {
						$newHeight = $height;
						if ($keepProportion) {
							$newWidth = $height * $ratio;
						}
					}
				}
			}

			$resizedImage = imagecreatetruecolor($newWidth, $newHeight);
			imagealphablending($resizedImage, false);
			imagesavealpha($resizedImage, true);

			if ($this->mimeType == "image/png") {
				if ($transparency) {
					$transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
					imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
				} else {
					imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, imagecolorallocate($resizedImage, 255, 255, 255));
				}
			}

			$resized = imagecopyresampled($resizedImage, $this->imageObject, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);

			if ($resized) {
				$this->imageObject = $resizedImage;
			}
		}
	}

	public function makeBlack()
	{
		$image = imagecreatetruecolor($this->width, $this->height);
		imagecolorallocate($image, 0, 0, 0);
		$this->imageObject     = $image;
		$this->mimeType = "image/png";
	}

	public function createVideoThumbnail($video) {
		if (!$video) return false;

		$path = 'PATH="$PATH:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/usr/local/git/bin" ';
		$ffmpeg = trim(shell_exec("$path which ffmpeg"));

		if ($ffmpeg) {
			$videoInfo = [];
			// get information about the video file
			exec("$ffmpeg -i $video 2>&1", $videoInfo);

			$frame = "00:00:01.000";
			
			// find the line starting with Duration
			foreach ($videoInfo as $infoLine) {
				$infoLine = trim($infoLine);
				if (strpos($infoLine, "Duration") === 0) {
					// string looks like this:
					// Duration: 00:05:38.58, start: 0.000000, ...
					// so just explode it with ',' and get the first item
					$duration = explode(",", $infoLine)[0];
					// slice of the Duration: part
					$duration = substr($duration, 9);
					// slice of the space if there is one
					$duration = trim($duration);

					// now explode the 00:05:38.58
					$duration = explode(":", $duration);
					// convert it to seconds
					$toSeconds = $duration[0] * 3600;
					$toSeconds += $duration[1] * 60;
					$toSeconds += $duration[2];

					// extract frame from the beginning of 10th second
					$secondToExtract = 10;

					// if video isn't even 10 seconds long, extract the middle frame
					if ($toSeconds < 10) {
						$secondToExtract = $toSeconds / 2;
					}

					// convert it to an array, that looks like this:
					// [0, 2, 49]
					$newFrame = [
						floor($secondToExtract / 3600),
						floor($secondToExtract % 3600 / 60),
						floor($secondToExtract % 3600 % 60),
					];

					// pad the values with 0, if they don't have it
					// ["00", "02", "49"]
					$newFrame = array_map(function($timeValue) {
						return str_pad($timeValue, 2, "0", STR_PAD_LEFT);
					}, $newFrame);

					// join the array into string that will be passed as an frame argument to ffmpeg
					// "00:02:49"
					$frame = implode(":", $newFrame);
				}
			}

			$tmp = create_tmp_file("jpg");
			unlink($tmp); // remove it, so just the name is preset for ffmpeg to write to
			$exec = shell_exec("$ffmpeg -i $video -ss $frame -vframes 1 $tmp");
			return $tmp;
		}

		return false;
	}

	public function setFile($item)
	{
		ini_set("memory_limit", "256M");
		$mimeType = $this->getFileMimeType($item);

		if (explode("/", $mimeType)[0] == "video") {
			$thumbnail = self::createVideoThumbnail($item);

			// if video thumbnail was successfully generated, continue with the video thumbnail
			// and "lie" about it
			if ($thumbnail) {
				$item = $thumbnail;
				$mimeType = "image/jpg";
			} else {
				return false; // otherwise, thumbnail could not be generated
			}
		}

		if (!$item) return false;

		$imageSize = getimagesize($item);
		if (!$imageSize)
		{
			$imageSize = [64, 64];
		}
		$this->width    = $imageSize[0];
		$this->height   = $imageSize[1];
		$this->mimeType = $mimeType;

		$imageObject = $this->createResourceFromType($mimeType, $item);
		$this->imageObject = $imageObject;
		$this->imagePath = $item;

		return !!$imageObject;
	}

	public function setContent($string) {
		$imageSize = getimagesizefromstring($string);
		if (!$imageSize) {
			$imageSize = [64, 64];
		}

		$this->width       = $imageSize[0];
		$this->height      = $imageSize[1];
		$this->imageObject = $this->createResourceFromString($string);
		$this->mimeType    = "image/jpeg";

		return !!$this->imageObject;
	}

	public function output($filename = false, $transparency = true)
	{
		if (!$this->imageObject) {
			$this->makeBlack();
		}

		if ($filename) {
			return $transparency ? imagepng($this->imageObject, $filename, 9) : imagejpeg($this->imageObject, $filename);
		} else {
			$mimeType = $this->mimeType;
			
			if ($mimeType) header("Content-Type: $mimeType");
			echo $transparency ? imagepng($this->imageObject, null, 9) : imagejpeg($this->imageObject);
			die(imagedestroy($this->imageObject));
		}
	}
}
