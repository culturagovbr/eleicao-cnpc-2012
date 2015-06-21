<?php
/**
 * Login e autenticao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class AdminController extends GenericController
{

    private $diretorioUpload = null;
    private $caminhoAcessoArquivo = null;
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    public function indexAction()
    {
        $auth  = Zend_Auth::getInstance();
        
        if(!$auth->hasIdentity()){
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    }

    public function salvarAvalicaoFinalInscritoAction()
    {
        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }

        //perfil minico que pode acessa essa pagina
        parent::perfilMinimo(4); //perfil administrador 

        $tbCadastro = new Cadastro();
        $arrResultado = $tbCadastro->buscarResultadoFinalAvaliacao();


        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $tbAvaliacaoFinal = new AvaliacaoFinalInscrito();
        $where = array();
        $tbAvaliacaoFinal->delete($where);
        try{

            $db->beginTransaction();

            foreach ($arrResultado as $registro){

                $arrDados['id_usuario']         = $registro->id_usuario;
                $arrDados['id_setorial']        = $registro->id_setorial;
                $arrDados['id_cadastro']        = $registro->id_cadastro;
                $arrDados['vhr_nomeinscrito']   = $registro->nome_inscrito;
                $arrDados['chr_uf']             = $registro->uf;
                $arrDados['chr_avaliacao_eleitor']   = $registro->avaliacao_eleitor;
                $arrDados['chr_avaliacao_candidato'] = $registro->avaliacao_candidato;
                $arrDados['int_tipocadastro']        = $registro->int_tipocadastro;
                $tbAvaliacaoFinal->insert($arrDados);
            }
            
            //xd($arrResultado);
            
            $db->commit();
            parent::message("Operação realizada com sucesso", "/admin", "CONFIRM");

        }catch(Exception $e){
            $db->rollBack();
            parent::message("Erro ao realizar operação. ".$e->getMessage(), "/admin", "ERROR");
        }
    }

    public function arquivosAction(){
        $cpf = "04189792438,
32090277149,
01423020669,
27788822272,
48880086472,
43935168349,
02183630957,
52963560044,
42344646949,
15996603268,
01714387798,
31675283893,
00612652173,
70258724668,
23033878172,
18607888234,
75516837200,
16006240491,
02853915778,
42610966415,
49729330034,
05868091710,
11080820760,
38337029749,
29651662700,
05174470708,
20292430000,
66294819091,
00164538089,
00607230096,
73461784249,
67175643349,
00280692340,
97201839187,
42857724934,
28158728898,
48804207515,
29763967520,
11977264115,
72656018153,
53272668691,
64704726215,
63774046204,
85921343404,
09065373802,
50372319904,
61098639987,
01026041422,
01097749738,
01419274880,
75776952468,
30034728449,
61037591704,
27860442191,
13870238020,
04689046204,
04298248828,
83932216504,
79150632434,
69782504220,
84362260110,
00346673933,
93927401587,
80524788804,
04279872880,
02394194723,
44529309487,
00888280424,
96790687572,
78814243700,
14408159840,
09798463234,
68972482234,
12103373200,
44223676415,
32968000772,
20087560410,
46745696053,
29280029878,
00631259864,
95413197853,
54315182400,
29492335549,
06270071470,
18167853268,
22545428850,
60673150097,
00320202879,
96230550482,
02502768799,
07094209860,
60650311868,
59921188968,
11285508220,
01265672474,
01469811707,
13291050282,
92300383268,
13290908291,
72175753204,
13037714204,
12276138220,
17081874215,
72198729334,
82719071153,
06741273824,
13113038391,
42784565300,
10358389666,
75987546915,
05522361603,
62207920259,
09112306703,
15723798200,
22450211772,
77718364968,
55642276972,
05543789572,
04110767873,
17951639842,
04940808416,
01898592454,
80071309500,
23187930506,
18749429515,
42549256387,
42395518387,
79465811734,
83384634187,
05836746664,
42600227253,
25216090568,
59355611404,
15230139404,
05646967902,
72116463220,
15364111830,
34720685404,
22421165504,
79267220578,
42498180397,
09684390718,
01199880701,
42336643120,
98659448715,
34361014604,
14235790200,
36001562687,
03418689493,
02852710919,
01804525928,
69172935049,
25780000000,
06806686834,
01315703823,
04863265867,
05701480810,
68993170100,
99476886153,
51567385168,
03457899703,
30207665168,
38597268115,
17587808604,
48753564634,
45857822149,
41263120059,
47061332387,
55015832753,
38779420753,
22514040230,
42711347087,
26426412015,
80427456053,
82201781915,
06067786087,
33993351053,
49833170900,
37575635904,
42910838404,
31131271572,
09469281004,
27970851835,
04240925819,
29907030805,
07505463870,
01314842811,
48133850797,
16796250478,
07756750403,
17739209449,
78499828272,
01093765445,
21998388468,
42474337415,
95206531472,
89048083753,
00331625148,
22004647892,
13349490425,
35430745472,
40747840415,
40510344453,
06804845404,
07276438430,
71248099249,
95428577215,
51456052500,
00831286547,
36278505720,
39461750749,
76965732334,
06167694664,
39166520100,
03207191622,
42976405204,
67357385272,
14742110463,
36697281715,
25825453768,
24482366749,
40673693791,
00468906029,
03450080733,
90246535920,
01586590901,
34162076880,
14689507899,
07604840400,
91138248568,
38712822787,
10953299520,
22353631134,
06686828688,
03223704698,
05483483677,
04036114662,
73540080600,
89256875691,
71868380734,
00384772706,
02081797712,
20867700068,
07626983904,
49291262072,
53846559920,
07588596864,
39343634820,
29062086802,
53185374800,
71056092572,
81530765404,
93297408553,
19574320200,
08401586453,
75043955449,
51990067034,
02398604977,
88936007815,
05641464473,
14570246591,
69942218149,
97748340110,
66690900134,
73937231234,
13295390444,
08972270423,
09700214451,
01066212465,
09934544458,
77821246700,
90906489768,
06891732450,
42076218220,
22555315268,
84591692868,
00222431008,
54250765091,
94518491904,
79155863949,
11346994846";

        $arr = explode(",",trim($cpf));
        //xd($arr);


        $this->diretorioUpload = getcwd() . "/public/anexos/";
        $this->caminhoAcessoArquivo = Zend_Controller_Front::getInstance()->getBaseUrl() . "/public/anexos/";

        /*======== VERIFICA CANDIDATOS SEM CURRICULO ================*/
        /*$nomeArquivo = "curriculo.pdf";
        foreach($arr as $chave => $cpf){
            $urlArquivo = $this->diretorioUpload.trim($cpf)."/".$nomeArquivo;
            if(!file_exists($urlArquivo)){
                $arrCurriculo[] = trim($cpf);
            }
        }
        //xd($arrCurriculo);
        $tbAvalcao = new AvaliacaoFinalInscrito();
        $where = array();
        $where['ci.id_item = 3 and ci.vhr_valor IN (?)'] = $arrCurriculo; //campo cpf no formulario
        $rsCurriculo = $tbAvalcao->buscarCompletaCandidatos($where, array());
        foreach($rsCurriculo as $registro){
            $cpfSemCurriculo .= "'".$registro->vhr_valor."',";
        }
        $cpfSemCurriculo = substr($cpfSemCurriculo, 0, strlen($cpfSemCurriculo)-1);
        xd($cpfSemCurriculo);
         */

        /*======== VERIFICA CANDIDATOS SEM PORTFOLIO ================*
        $nomeArquivo = "portfolio.pdf";
        foreach($arr as $chave => $cpf){
            $urlArquivo = $this->diretorioUpload.trim($cpf)."/".$nomeArquivo;
            if(!file_exists($urlArquivo)){
                $arrPortfolio[] = trim($cpf);
            }
        }
        //xd($arrPortfolio);
        $tbAvalcao = new AvaliacaoFinalInscrito();
        $where = array();
        $where['ci.id_item = 3 and ci.vhr_valor IN (?)'] = $arrPortfolio; //campo cpf no formulario
        $rsPortfolio = $tbAvalcao->buscarCompletaCandidatos($where, array());
        foreach($rsPortfolio as $registro){
            $cpfSemPortifolio .= "'".$registro->vhr_valor."',";
        }
        $cpfSemPortifolio = substr($cpfSemPortifolio, 0, strlen($cpfSemPortifolio)-1);
        x($cpfSemPortifolio);
        xd($rsPortfolio->count());
        xd('para');*/

        /*======== VERIFICA CANDIDATOS SEM CARTA APOIO ================*/
        $nomeArquivo = "carta_apoio.pdf";
        foreach($arr as $chave => $cpf){
            $urlArquivo = $this->diretorioUpload.trim($cpf)."/".$nomeArquivo;
            if(!file_exists($urlArquivo)){
                $arrCartaApoio[] = trim($cpf);
            }
        }
        //xd($arrCartaApoio);
        $tbAvalcao = new AvaliacaoFinalInscrito();
        $where = array();
        $where['ci.id_item = 3 and ci.vhr_valor IN (?)'] = $arrCartaApoio; //campo cpf no formulario
        $rsCartaApoio = $tbAvalcao->buscarCompletaCandidatos($where, array());
        foreach($rsCartaApoio as $registro){
            $cpfSemCartaApoio .= "'".$registro->vhr_valor."',";
        }
        $cpfSemCartaApoio = substr($cpfSemCartaApoio, 0, strlen($cpfSemCartaApoio)-1);
        x($cpfSemCartaApoio);
        xd($rsCartaApoio->count());
        xd('para');
    }

} // fecha class