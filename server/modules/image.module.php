<?php
/*
 * Xibo - Digital Signage - http://www.xibo.org.uk
 * Copyright (C) 2006,2007,2008 Daniel Garner and James Packer
 *
 * This file is part of Xibo.
 *
 * Xibo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Xibo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Xibo.  If not, see <http://www.gnu.org/licenses/>.
 */
class image extends Module
{
    // Custom Media information
    protected $maxFileSize;
    protected $maxFileSizeBytes;

    public function __construct(database $db, user $user, $mediaid = '', $layoutid = '', $regionid = '', $lkid = '')
    {
        // Must set the type of the class
        $this->type= 'image';
        $this->displayType = __('Image');

        // Get the max upload size from PHP
        $this->maxFileSize 	= ini_get('upload_max_filesize');
        $this->maxFileSizeBytes = convertBytes($this->maxFileSize);

        // Must call the parent class
        parent::__construct($db, $user, $mediaid, $layoutid, $regionid, $lkid);
    }

    /**
     * Sets the Layout and Region Information
     *  it will then fill in any blanks it has about this media if it can
     * @return
     * @param $layoutid Object
     * @param $regionid Object
     * @param $mediaid Object
     */
    public function SetRegionInformation($layoutid, $regionid)
    {
        $db =& $this->db;
        $this->layoutid = $layoutid;
        $this->regionid = $regionid;
        $mediaid = $this->mediaid;
        $this->existingMedia = false;

        if ($this->regionSpecific == 1) return;

        // Load what we know about this media into the object
        $SQL = "SELECT storedAs FROM media WHERE mediaID = $mediaid ";

        if (!$row= $db->GetSingleRow($SQL))
        {
            trigger_error($db->error());
            return false;
        }

        $storedAs   = $row['storedAs'];

        // Any Options
        $this->SetOption('uri', $storedAs);

        return true;
    }

    /**
     * Return the Add Form as HTML
     * @return
     */
    public function AddForm()
    {
        return $this->AddFormForLibraryMedia();
    }

    /**
     * Return the Edit Form as HTML
     * @return
     */
    public function EditForm()
    {
        return $this->EditFormForLibraryMedia();
    }

    /**
     * Add Media to the Database
     * @return
     */
    public function AddMedia()
    {
        return $this->AddLibraryMedia();
    }

    /**
     * Edit Media in the Database
     * @return
     */
    public function EditMedia()
    {
        return $this->EditLibraryMedia();
    }

    public function Preview($width, $height)
    {
        if ($this->previewEnabled == 0)
            return parent::Preview ($width, $height);
        
        // Show the image - scaled to the aspect ratio of this region (get from GET)
        return sprintf('<div style="text-align:center;"><img src="index.php?p=module&q=GetImage&id=%d&width=%d&height=%d&dynamic" /></div>', $this->mediaid, $width, $height);
    }

    public function HoverPreview()
    {
        // Default Hover window contains a thumbnail, media type and duration
        $output = parent::HoverPreview();
        $output .= '<div class="hoverPreview">';
        $output .= '    <img src="index.php?p=module&q=GetImage&id=' . $this->mediaid . '&width=200&height=200&dynamic=true" alt="Hover Preview">';
        $output .= '</div>';

        return $output;
    }
    
    /**
     * Get Resource
     */
    public function GetResource($displayId = 0)
    {
    	$this->ReturnFile();
        
        exit();
    	
    }
}
?>
