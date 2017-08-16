<?
class FactoresController extends AppController{
	public $uses = array('Factor');
	//var $scaffold;
	public $helpers = array('html');
	//var $name = 'Personas';
	
	public $colorConcepto = array(2=>'#EAF1DD', 4=>'#D6E3BC', 6=>'#C2D69B', 8=>'#92D050', 10=>'#00B050');
	
	var $componets = array('Session', 'Auth');
	
	public function beforeFilter(){ 
		parent::beforeFilter();
		//$this->Auth->allow('index', 'preguntasFactor');
	}
	
	public function index(){
		$this->set('listaFactores', $this->Factor->find('all') );
	}
	
	public function preguntasFactor($id=null){
		$this->Factor->recursive=4;
		//$listaPreguntas = $this->Factor->find('first', array('Factor.id' => $id));
		///$listaPreguntas = $this->Factor->find('first', $id);
		$listaPreguntas = $this->Factor->find(array('Factor.id' => $id));
		$this->set('listaPreguntas', $listaPreguntas);
		$this->set('colorConcepto', $this->colorConcepto);
		
	}
	
		
}

?>