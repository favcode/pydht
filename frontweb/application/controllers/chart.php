<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class Chart extends Front_Controller {

    function __construct(){
		parent::__construct();
		$this->load->model("monitor_model","monitor");
        $this->load->model('application_model','app');
        $this->load->model('servers_model','server');
        $this->load->model('chart_model','chart');
        
	}

    
    public function index(){
        
        $data["server"]=$servers=$this->server->get_total_record_usage();
        $server_id = $this->uri->segment(3);
        $server_id=!empty($server_id) ? $server_id : "";
        if(empty($server_id)){
            if(!empty($servers)){
            $server_id=$servers[0]['id'];
            }
            else{
                $server_id=0;
            }
        }
        
        //连接数图表
        $reslut_status=array();
        for($i=30;$i>=0;$i--){
            $timestamp=time()-60*$i;
            $time= date('Y-m-d H:i',$timestamp);
            $reslut_status[$i]['time']=date('H:i',$timestamp);
            $dbdata=$this->chart->get_status($server_id,$time);
            $reslut_status[$i]['connections'] = $dbdata['connections'];
            $reslut_status[$i]['active'] = $dbdata['active'];
            
        }
        //print_r($reslut_active);exit;
        $data['reslut_status']=$reslut_status;
        
        $reslut_status_ext=array();
        for($i=30;$i>=0;$i--){
            $timestamp=time()-60*$i;
            $time= date('Y-m-d H:i',$timestamp);
            $reslut_status_ext[$i]['time']=date('H:i',$timestamp);
            $dbdata=$this->chart->get_status_ext($server_id,$time);
            $reslut_status_ext[$i]['qps'] = $dbdata['QPS'];
            $reslut_status_ext[$i]['tps'] = $dbdata['TPS'];
            $reslut_status_ext[$i]['Bytes_received'] = $dbdata['Bytes_received'];
            $reslut_status_ext[$i]['Bytes_sent'] = $dbdata['Bytes_sent'];
            
        }
        //print_r($reslut_active);exit;
        $data['reslut_status_ext']=$reslut_status_ext;
        $data['cur_nav']='chart_index';
        $data['cur_server_id']=$server_id;
        $data["cur_server"] = $this->server->get_servers($server_id);
        $this->layout->view('chart/index',$data);
    }
    
    public function detail(){

        $server_id=$this->uri->segment(3);
        if(!$server_id){
            $server_id=$_GET['server_id'];
            $time=$_GET['time'];
        }
        
        
        
        //echo $server_id;exit;
        if(empty($server_id)){
            redirect(site_url('chart/index'));
        }
        if(empty($time)){
            $time=3600;
        }
        $data["server"]=$this->server->get_total_record_usage();
        $data["server_id"]=$server_id;
        $data["time"]=$time;
        $data['cur_nav']='chart_index';
        $this->layout->view('chart/detail',$data);
    }
    
}

/* End of file chart.php */
/* Location: ./application/controllers/chart.php */