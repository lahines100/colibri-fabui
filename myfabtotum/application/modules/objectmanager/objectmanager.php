<?php 

class Objectmanager extends Module {

	public function __construct()
	{
		parent::__construct();
        
        ini_set( 'error_reporting', E_ALL );
			ini_set( 'display_errors', true );

		$this->layout->add_css_file(array('src'=>'application/modules/objectmanager/assets/css/filemanager.css', 'comment'=>'css for filemanager module'));

	}

	public function index(){

		//carico X class database
		$this->load->database();
		$this->load->model('objects');

		//carico helpers
		//$this->load->helper('ft_file_helper');
		$this->load->helper('smart_admin_helper');
        $this->load->helper('ft_date_helper');
        
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/jquery.dataTables-cust.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/ColReorder.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/FixedColumns.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/ColVis.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/ZeroClipboard.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/media/js/TableTools.min.js', 'comment'=>''));
        $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/DT_bootstrap.js', 'comment'=>''));
       
       
       
     
        
        $_table = $this->load->view('index/table', '', TRUE);
        
        $_widget_table = widget('objects'.time(), 'Objects',  '', $_table, false, true);
        

		$js_in_page = $this->load->view('index/js', '', TRUE);
		$this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => 'INDEX FUNCTIONS'));

		

        
        $data['_table'] = $_widget_table;
        
        
		$this->layout->view('index/index', $data);
        
	}



	public function add(){

		if($this->input->post()){
			
			//carico X class database
			$this->load->database();
			$this->load->model('objects');
            
            
			$_obj_data['obj_name']        = $this->input->post('name');
			$_obj_data['obj_description'] = $this->input->post('description');

			//inserisco il nuogo oggetto
			$_obj_id = $this->objects->insert_obj($_obj_data);

			//inserisco gli eventuali file dell'oggetto
			$files     = explode(',', $this->input->post('files'));
            
            $usb_files = explode(',', $this->input->post('usb_files'));
            
            
            
            
			
            $usb_files_id = array();
            
            foreach($usb_files as $file){
                if($file != ''){
                   array_push($usb_files_id, $this->copy_from_usb($file)); 
                }

            }
            
            
            $this->objects->insert_files($_obj_id, $files);
            $this->objects->insert_files($_obj_id, $usb_files_id);

			//torno all'homepage del modulo
			//$this->session->set_flashdata('obj_inserted', 'New object '.$_obj_data['obj_name'].' was inserted with success');
			redirect('objectmanager');		
		}

		//carico file configurazione
		$this->config->load('upload');
        
        
        /** LOAD FROM USB DISK FIRST TREE */
        $_destination = '/var/www/myfabtotum/application/modules/objectmanager/temp/media.json';
        $_command     = 'sudo python /var/www/myfabtotum/python/usb_browser.py  --dest='.$_destination;
        shell_exec($_command);
        //sleep ( 1);
        
        $data['folder_tree'] = json_decode(file_get_contents($_destination), TRUE);
        
        
        
        

		$js_data['accepted_files'] = $this->config->item('upload_accepted_files');
		$j_data['_upload_max_filesize'] = ini_get("upload_max_filesize");

		$data['_upload_max_filesize'] =  ini_get('upload_max_filesize');

        $this->layout->add_js_file(array('src'=> 'application/layout/assets/js/plugin/jquery-validate/jquery.validate.min.js', 'comment' => 'VALIDATE FORM'));
		$this->layout->add_js_file(array('src'=> 'application/layout/assets/js/plugin/dropzone/dropzone.min.js', 'comment' => 'DROPZONE JAVASCRIPT'));

		$js_in_page = $this->load->view('add/js', $js_data, TRUE);
		$this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => 'INIT DROPZONE'));


		$cc_in_page = $this->load->view('add/css', '', TRUE);
		$this->layout->add_css_in_page(array('data'=> $cc_in_page, 'comment' => ''));
        

		$this->layout->view('add/index', $data);

	}


	public function edit($id_object){
	   
       /** LOAD DATABASE */
       $this->load->database();
	   $this->load->model('objects');
       $this->load->model('files');
       
       /** LOAD HELPERS */
       $this->load->helper('smart_admin_helper');
       $this->load->helper('ft_date_helper');
       $this->load->helper('ft_file_helper');
       
       if($this->input->post()){
            $this->objects->update($id_object, $this->input->post());
       }
       
       /** LOAD OBJECT */
       $_object = $this->objects->get_obj_by_id($id_object);
       
       /** LOAD FILES ID */
       $_files_id = $this->objects->get_files($id_object);
       $_files = array();
       
       foreach($_files_id as $id){
        
        $_files[] = $this->files->get_file_by_id($id);
        
       }
       
       $printable_files[] = '.gc';
       $printable_files[] = '.gcode';
       $printable_files[] = '.nc';
       
      
       $_widget_data['_id_object']       = $id_object;
       $_widget_data['_files']           = $_files;
       $_widget_data['_printable_files'] = $printable_files;
       
       
       /** LOAD TABLE CONTENT */
       $_table = $this->load->view('edit/table', $_widget_data, TRUE);
       
       /** CREATE WIDGET */
       $_widget_table = widget('objects'.time(), 'Files',  '', $_table, false, true);
       
       
       
       /** LAYOUT */
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/jquery.dataTables-cust.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/ColReorder.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/FixedColumns.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/ColVis.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/ZeroClipboard.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/media/js/TableTools.min.js', 'comment'=>''));
       $this->layout->add_js_file(array('src'=>'application/layout/assets/js/plugin/datatables/DT_bootstrap.js', 'comment'=>''));
       
       $js_in_page = $this->load->view('edit/js', '', TRUE);
       $this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => 'EDIT FUNCTIONS'));
       
       
       $data['_object'] = $_object;
       $data['_widget'] = $_widget_table;
       
       
	   
	   $this->layout->view('edit/index', $data);

	}


	public function delete($obj_id){

		//se la chiamata � di tipo ajax allora posso fare...
		if($this->input->is_ajax_request()){
				
			//carico X class database
			$this->load->database();
			$this->load->model('objects');
			$this->load->model('files');
				
			$object = $this->objects->get_obj_by_id($obj_id);
			$files  = $this->objects->get_files($obj_id);

			//cancello l'oggetto
			if($this->objects->delete($obj_id)){

				//cancello i record dei file nella tabella di appoggio
				$this->objects->delete_files($obj_id, $files);
				
				//cancello i file
				foreach($files as $file){
					$this->files->delete($file);
				}
				//unlink($file->full_path);
				echo json_encode(array('success' => TRUE, 'messagge'=>''));

			}
				

		}else{
			echo "call not valid";
		}
	}


	public function upload(){

		//carico file configurazione
		$this->config->load('upload');

		$upload_dir = $this->config->item('upload_dir');
		$accepted_files = str_replace('.', '', $this->config->item('upload_accepted_files'));
		$accepted_files = str_replace(',', '|', $accepted_files);


		/*
		 * Verifico l'estensione del file in modo da salvarlo nell cartella esatta, se la cartella non esiste la creo
		*/
		$_tmp_file_name = explode('.', $_FILES['file']['name']);
		$_extension     = end($_tmp_file_name);

		if(!file_exists($upload_dir.$_extension)) // se la cartella non esiste la creo
		{
			mkdir($upload_dir.$_extension, 0777);
		}

		$config['upload_path'] = $upload_dir.$_extension;
		$config['allowed_types'] = $accepted_files;

		//carico la libreria per la gestione dell'upload
		$this->load->library('upload', $config);


		if ( ! $this->upload->do_upload('file'))
		{
			$error = array('error' => $this->upload->display_errors());

			print_r($error);
		}
		else
		{
			$data = $this->upload->data();
				
			//carico X class database
			$this->load->database();
			$this->load->model('files');
			
            /** LOAD FILE HELPER */
            $this->load->helper('ft_file_helper');
            
            /** UTIL PARAMS */
            $_printable_files[] = '.gc';
            $_printable_files[] = '.gcode';
            $_printable_files[] = '.nc';
            
            /** IF IS A PRINTABLE FILE CHECK THE TYPE OF PRINT - ADDITIVE O SUBTRACTIVE */
            if(in_array($data['file_ext'], $_printable_files)){
                $data['print_type'] = print_type($data['full_path']);
            }
            
            
			echo $this->files->insert_file($data);
				
		}

	}


	function select($mode){

		//carico X class database
		$this->load->database();
		$this->load->model('files');


		$data['_files'] = $this->files->get_all();


		$this->load->view('select/'.$mode, $data);
	}
	
	
	
	function object($id){
		
		$this->load->helper('ft_date_helper');

        
        $printable = $this->input->post('printable');
        
        $printable_files[] = '.gc';
        $printable_files[] = '.gcode';
        $printable_files[] = '.nc';
		
		//carico X class database
		$this->load->database();
		$this->load->model('objects');
		$this->load->model('files');
		
		
		$_object     = $this->objects->get_obj_by_id($id);
		$_obj_files  = $this->objects->get_files($id);
		
		$_files = array();
		
		
		$_object->date_insert  = mysql_to_human($_object->date_insert);
		$_object->date_updated = mysql_to_human($_object->date_updated);
		
		
		
		
		foreach($_obj_files as $_file){
			
			
			$_temp = $this->files->get_file_by_id($_file);
			
			if($_temp != ''){
			 
                if($printable && in_array($_temp->file_ext, $printable_files)){
                    $_files[$_file] = $_temp;
                }
				
            }
			
		}
		echo json_encode(array('object'=>$_object, 'files'=>array('number' => count($_files), 'data' => $_files)));
		
		
		
		
	}
    
    
    function json(){
        
        //carico X class database
		$this->load->database();
		$this->load->model('objects');
        
        $this->load->helper('ft_date_helper');
        
        $objects = $this->objects->get_all();
        
        $rows = array();
        
        foreach($objects as $obj){

            $_edit_button   = '<a href="'.site_url('objectmanager/edit/'.$obj->id).'" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a>';
            $_delete_button = '<a href="javascript:ask_delete('.$obj->id.', \''.$obj->obj_name.'\');" file-id="'.$obj->id.'" file-name="'.$obj->obj_name.'" class="btn btn-default btn-xs file-delete txt-color-red"><i class="fa fa-times"></i></a>';
            
            
            $icon_file = $obj->num_files > 1 ? 'fa-files-o' : ' fa-file-o';
            $_files = $obj->num_files.' <i class="fa '.$icon_file.'"></i>';
            
            $rows[] = array($obj->obj_name, $obj->obj_description, mysql_to_human($obj->date_insert), $_files,  $_edit_button.' '.$_delete_button);          
        }
        
        
        echo json_encode(array('aaData' => $rows));
        
    }
    
    
    
    
    
    public function file($action, $object_id = 0, $file_id = 0){
        
        
        $data['_object_id'] = $object_id;
        $data['_file_id']   = $file_id;
        
        
        if($action == 'add'){
            
            
            if($this->input->post()){
                
                //carico X class database
        		$this->load->database();
        		$this->load->model('objects');
                
                $files = explode(',', $this->input->post('files'));
                $usb_files = explode(',', $this->input->post('usb_files'));
                
                $usb_files_id = array();
            
                foreach($usb_files as $file){
                    if($file != ''){
                       array_push($usb_files_id, $this->copy_from_usb($file)); 
                    }
    
                }
                
                $this->objects->insert_files($this->input->post('object'), $files);
                $this->objects->insert_files($this->input->post('object'), $usb_files_id);
                
                
                redirect('objectmanager/edit/'.$this->input->post('object'), 'location');
                
                
            }
            
            
            /** LOAD UPLOAD CONFIG */
            $this->config->load('upload');
            
            $js_data['accepted_files']       = $this->config->item('upload_accepted_files');
            $js_data['_upload_max_filesize'] = ini_get("upload_max_filesize");
            $js_data['_action'] = $action;
            $js_data['_object_id'] = $object_id;
            $js_data['_time'] = $data['_time'] = time();
             
            
            $this->layout->add_js_file(array('src'=> 'application/layout/assets/js/plugin/dropzone/dropzone.min.js', 'comment' => 'DROPZONE JAVASCRIPT'));
            
            $js_in_page = $this->load->view('file/js_add', $js_data, TRUE);
            $this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => 'INIT DROPZONE'));
            
            
            /** LOAD FROM USB DISK FIRST TREE */
            $_destination = '/var/www/myfabtotum/application/modules/objectmanager/temp/media.json';
            $_command     = 'sudo python /var/www/myfabtotum/python/usb_browser.py  --dest='.$_destination;
            shell_exec($_command);
            //sleep ( 1);
            
            $data['folder_tree'] = json_decode(file_get_contents($_destination), TRUE);
            
            
            $data['_action'] = $action;
            
            
        }
        
        
        
        if($action == "view"){
            
            ini_set( 'error_reporting', E_ALL );
			ini_set( 'display_errors', true );
            
            /** LOAD HELPER */
            $this->load->helper('ft_file_helper');
            
            //carico X class database
      		$this->load->database();
        	$this->load->model('files');
            $file = $this->files->get_file_by_id($file_id);
            
            
            $data['_success'] = false;
            
            if($this->input->post()){
                //print_r($this->input->post());
                //exit();
                echo $this->input->post('file_content')."<br>";
                $file_content = urldecode($this->input->post('file_content'));
                echo $file_content;
                exit();
                
                file_put_contents($file->full_path, $file_content, FILE_USE_INCLUDE_PATH); 
                $data['_success'] = true;
            }
            
            
            
            $data['_file'] = $file;
            $data['_file_content'] = file_get_contents($file->full_path, FILE_USE_INCLUDE_PATH);
            
            
            
           // print_r($file);
           $js_in_page = $this->load->view('file/js_view', $data, TRUE);
           $css_in_page = $this->load->view('file/css_view', '', TRUE);
           
           /** LAYOUT SETUP */
           $this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => '')); 
           $this->layout->add_css_in_page(array('data'=> $css_in_page, 'comment' => '')); 
           $this->layout->add_js_file(array('src'=> 'application/layout/assets/js/plugin/ace/src-min/ace.js', 'comment' => 'ACE EDITOR JAVASCRIPT')); 
           
           $this->layout->set_compress(false); 
            
        }
        
        

        $this->layout->view('file/'.$action, $data);
        
    }
    
    
    
    public function download($id_file){
        
        //carico X class database
		$this->load->database();
		$this->load->model('files');
        
        /** LOAD HELPER */
        $this->load->helper('download');
        
        
        $_file = $this->files->get_file_by_id($id_file);
        
        $data = file_get_contents($_file->full_path); // Read the file's contents
        
        force_download($_file->file_name, $data);

    }
    
    
    
    public function delete_file($id_file){
        
        if($this->input->is_ajax_request()){
				
			//carico X class database
			$this->load->database();
			$this->load->model('files');
            $this->load->model('objects');
				
			$_file = $this->files->get_file_by_id($id_file);
            $id_object = $this->objects->get_by_file($id_file);
            
            
            $this->objects->delete_files($id_object, array($id_file));
            
            $this->files->delete($id_file);
            
            echo json_encode(array('success' => TRUE, 'messagge'=>''));
		

		}else{
			echo "call not valid";
		}
        
    }
    
    
    /**
     * 
     * PREAPARE FILE,  
     * 
     */
    function prepare($type, $object, $file){
        
        switch($type){
            
            
            case 'stl':
                $this->g_code($object, $file);
                break;
            
        }
        
        
        //$this->layout->view('prepare/index');
        
    }


    
    /**
     *  CREATE GCODE FROM STL FILE
     * 
    */ 
    function g_code($object, $file){
        
        
        //carico X class database
    	$this->load->database();
    	$this->load->model('files');
        $this->load->model('configuration');
        
        $_file = $this->files->get_file_by_id($file);
        $_presets = json_decode($this->configuration->get_config_value('slicer_presets'), true);
        
        
        
        
        $data['_object']  = $object;
        $data['_file']    = $_file;
        $data['_presets'] = $_presets;
        
        $this->layout->add_js_file(array('src'=> 'application/layout/assets/js/plugin/ace/src-min/ace.js', 'comment' => 'ACE EDITOR JAVASCRIPT')); 
        
        $js_in_page = $this->load->view('prepare/gcode/js', $data, TRUE);
        $this->layout->add_js_in_page(array('data'=> $js_in_page, 'comment' => '')); 
        $this->layout->view('prepare/gcode/index', $data);
        
        
        
    }
    
    
    /** */
    function copy_from_usb($file){
       
       //TO DO ---> MOVE FROM USB TO UPLOAD FOLDER
       //TO DO ---> INSERT RECORD TO TB FILE
       
       /** LOAD FILE HELPER */
       $this->load->helper('file');
       $this->load->helper('ft_file_helper');
       
       //echo $file.'<br>';
       $file_name = explode("/", $file);
       $file_name = end($file_name);
      
      
       
       
       /** MOVE TO TEMP FOLDER */
       $_command = 'sudo cp '.$file.' /var/www/temp/'.$file_name;
       shell_exec($_command);
       
       $file = '/var/www/temp/'.$file_name;
       
       $file_information  = get_file_info($file);
       
       $file_extension    = get_file_extension($file);
       
       $folder_destination = '/var/www/upload/'.str_replace('.', '', $file_extension).'/';
       
       $file_name = set_filename($folder_destination, $file_name);
       
       //echo $file_extension.'<br>';
       //echo $file_name.'<br>';
       
       
       /** MOVE TO FINALLY FOLDER */
       $_command = 'sudo cp '.$file.' '.$folder_destination.$file_name;
       shell_exec($_command);
       /** ADD PERMISSIONS */
       $_command = 'sudo chmod 746 '.$folder_destination.$file_name;
       shell_exec($_command);
       
       
       /** INSERT RECORD TO DB */
       //carico X class database
        $this->load->database();
		$this->load->model('files');
        
        $data['file_name']  = $file_name;
        $data['file_path']  = $folder_destination;
        $data['full_path']  = $folder_destination.$file_name;
        $data['raw_name']   = str_replace($file_extension, '', $file_name);
        $data['orig_name']  = $file_name;
        $data['file_ext']   = $file_extension;
        $data['file_size']  = $file_information['size'];
        $data['print_type'] = print_type($folder_destination.$file_name);
       
       /** REMOVE TEMP FILE */
       unlink($file);
       
       
       
       
       /** RETURN  */
       return $this->files->insert_file($data);
      
      
     
        
    }



}

?>