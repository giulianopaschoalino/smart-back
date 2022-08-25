<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Users\UserContractInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserContractInterface $user
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $response = $this->user->withRelationsByAll('roles');
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->all();
            $data['password'] = bcrypt($request->password);

            if (!$request->hasFile('profile_picture')) {
                return $this->errorResponse(false, '', 500);
            }
            $file = $request->file('profile_picture');
            $path = $file->storeAs('avatars', $file->hashName(),'s3');

            $data['profile_picture'] =  Storage::disk('s3')->url($path);
            $response = $this->user->create($data);
            $response->roles()->sync($data['role']);
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $response = $this->user->find($id);
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $response = $this->user->update($request->all(), $id);
            return (new UserResource($response))
                ->response()
                ->setStatusCode(Response::HTTP_ACCEPTED);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $response = $this->user->destroy($id);
            return response()->json($response, Response::HTTP_NO_CONTENT);
        } catch (\Exception $ex) {
            return $this->errorResponse(false, $ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function importUserControll(Request $request) {
        $validated = $request->validate([
            'password' => 'string|nullable'
        ]);
        if($validated['password'] != '78s7*a77xghhsa5219129382(*728292SPsk%%%shssajlk')
        {
            return response()->json(['error' => true]);
        }

        $arrayCodCliente = [
            180101211,180102211,180103211,180104211,180105211,180107211,180108211,180110211,180111211,180112211,180113211,180114211,180115211,180116211,180118211,180119211,180120211,180121211,180122211,180123211,180124211,180125211,180126211,180128211,180129211,180130211,180131211,180201211,180203211,180204211,180205211,180206211,180208211,180327221,180328231,180329231,180716121,180717121,180718121,180719121,180912141,180913141,180914141,180915141,180916141,180209211,180211211,180215221,180216221,180217221,180218221,180219221,180220221,180222221,180224221,180225221,180226221,180227221,180228221,180302221,180303221,180304221,180305221,180306221,180307221,180309221,180310221,180311221,180312221,180314221,180316221,180317221,180318221,180319221,180320221,180323221,180324221,180325221,180326221,180330231,180331231,180402231,180403231,180404231,180405231,180406231,180407231,180408231,180409231,180410231,180411231,180412231,180413231,180414231,180415231,180416231,180417231,180418231,180419231,180420231,180421231,180422231,180423231,180424231,180426231,180427231,180428231,180430231,180501231,180503231,180506231,180507231,180508231,180512111,180513111,180514111,180515111,180516111,180517111,180518111,180521111,180522111,180523111,180524111,180525111,180527111,180528111,180529111,180530111,180602111,180603111,180604111,180605111,180606111,180607111,180608111,180609111,180610111,180612111,180613111,180614111,180615111,180616111,180617111,180618121,180619121,180620121,180621121,180622121,180623121,180624121,180625121,180626121,180627121,180628121,180629121,180630121,180701121,180702121,180703121,180704121,180706121,180707121,180708121,180709121,180710121,180711121,180712121,180713121,180714121,180715121,180721131,180722131,180723131,180724131,180725131,180726131,180727131,180728131,180729131,180730131,180731131,180801131,180802131,180805131,180806131,180808131,180809131,180810131,180811131,180812131,180813131,180814131,180815131,180816131,180817131,180818131,180819131,180820131,180821131,180822131,180823131,180825141,180826141,180828141,180829141,180830141,180901141,180902141,180904141,180905141,180906141,180907141,180908141,180909141,180910141,180917141,180918141,180919141,180920141,180921141,180922141,180923141,180924141,211112230,211112231,180925141,180926141,180927141,180928141,180929231,181005231,181006111,181007111,181009131,210426130,210428220,210512230,210519140,210611110,210713130,210720230,210819130,211126110,211214110,220125230,220302230,220310230,220517130];
        $arrayEmail = ['piturqueti@hotmail.com','controladoria@eletromil.com.br','rodrigo@antoniomoro.com.br','jackquesfran@supremace.com.br','ivogcost@gmail.com','edgarmirandafilho@hotmail.com','ricobonozo@yahoo.com.br','bfmenon@ceramicasantaizabel.com.br','edgarmirandafilho@hotmail.com','filipe.chinapark@gmail.com','gustavo@saboratta.com','sandro@iccsulparana.com.br','pantaleao@serradaprata.com.br','financeiro@ecoplastsacolas.com.br','fmfinanceirofilial2@hotmail.com','financeiro@novakcarnes.com.br','david@granortesa.ind.br','marcio.grutzmacher@hame.com.br','willian@herbarium.net','fabiatavares@hsmonica.com','kelston@jjpplasticos.com.br','ronaldo@kabel.com.br','anapaula@lacomercio.com.br','shaimon@pedreiracosta.com.br','proencol@jupiter.com.br','diego@playwood.com.br','poliana@polita.com.br','jair.freitas@gruporcarvalho.com.br','sampnet@gmail.com','manuel@pedrario.com.br','edgarmiranda@uol.com.br','racksonsp@hotmail.com','julianasartorio@refrigerantesuai.com.br','flavio.delorenzo@elis.com','gustavo.dias@aeroflex.ind.br','rpaduani@almavivadobrasil.com.br','dion@rederioverde.com.br','suprimentos@sobritaindustrial.com.br','joaofelipe@sorvetespaletitas.com.br','adm@superfae.com.br','fiscal@domarmando.com.br','sacoslukplast@gmail.com','nicolas@anamariana.com.br','gerencia@meani.com.br','bruno@mineriosfurquim.com.br','jairo.silva@grupovanguarda.com','bruna@mgn.ind.br','flavio.delorenzo@elis.com','paulo.alexandre@botafogo.ind.br','suprimentos@brasitalia.com.br','brunag.coutinho@gmail.com','rafaelaragao@caxiasshoppingcenter.com.br','marcos@ceramicanichele.com.br','edcmfilho@hotmail.com','jorge@cuecasduomo.com.br','claudio@laticiniosdamare.com.br','marco.curcio@dolfin.com.br','suprimentos@brasitalia.com.br','gerad@ferronorteindustrial.com.br','natanael.oliveira@fibrasa.com.br','marcio@friganso.com.br','gerencia@frutapolpa.com.br','gilberto@gaam.com.br','luciano@ghelplus.com.br','felipe.bugarim@buphotels.com.br','wnascimento@ns-group.com','flavio.delorenzo@elis.com','luiz.bersou@mash.com.br','eduardojunior@meller.com.br','suprimentos@brasitalia.com.br','comercial@naturaves.com.br','comercial@naturaves.com.br','yuri@superperim.com.br','marcopinto@pintos.com.br','raffaele@replaex.com.br','dir.geral@unibalsas.edu.br','jose.teixeira@unifacema.edu.br','xermona@unisulma.edu.br','motta@mottanet.com.br','rtf@granjafaria.com.br','sersil@brasilamarras.com','evandro@metalser.com.br','jusuchara04@hotmail.com','consbrita@consbrita.com.br','bernardo.kapich@coopeavi.coop.br','samantha.sathler@cimentonacional.com.br','andreia.dias@faesa.br','alex_logullo@thorgranitos.com.br','sup.compras@fibracem.com','acpereira@vallesul.com.br','henrique@gramafal.com','gerencia@grdgranitos.com.br','lucas@paranagran.com','leonardo@krindges.com.br','eduardo.dagostinho@logusquimica.com.br','jamila@centraldecomprasmartins.com.br','leandro@metalosa.com.br','evandro@metalser.com.br','janderson@usisteel.ind.br','paulo.veiga@gruposantaluzia.com.br','lucas@paranagran.com','apontamento02@prgrupoparana.com','cfminet@gmail.com','nortkar@nortkar.com.br','fabricio.araujo@novaformapvc.com.br','willian@prgrupoparana.com','lucas@paranagran.com','acpereira@vallesul.com.br','rogerio@unibeef.com.br','alex_logullo@thorgranitos.com.br','adson@toledogranitos.com.br','thiago@afort.com.br','juliano.leobet@coopagricola.coop.br','marcio@grupoararaazul.com.br','marcio@grupoararaazul.com.br','josueliraneto@hotmail.com','contabilidade@calcem.com.br','junior@sanfrancisco.agr.br','carlos.eduardo@caltec.com.br','iguimaraes@canonne.com.br','paulo@grupocorgraf.com.br','johnnydalvi@dalvistones.com','vilmar@dapi.com.br','kassiano.tridapalli@farol.ind.br','dennis@granjaeconomica.com.br','incal@incalcalcarios.com.br','carmen@internacionalegranite.com.br','walleska@itamil.com.br','marcel@itatinga.com.br','contabilidade@calcem.com.br','alicevitoriahotel@alicevitoriahotel.com.br','eliane@polical.com.br','amarildo@pollifertilizantes.com.br','cantidia.montebeler@provale.ind.br','cantidia.montebeler@provale.ind.br','cantidia.montebeler@provale.ind.br','giovani@rafainpalace.com.br','eliza_rionile@hotmail.com','josemarguarise@yahoo.com.br','fabiano_motin@hotmail.com','celso@supermercadovitor.com.br','sergiofroguel@terapapeis.com.br','jadson.morais@viatekbrasil.com.br','cezar@w3.ind.br','compras@acpmoveis.com.br','ludmila@andradesa.com.br','elizangela@supermercadofae.com.br','leandro@barripack.com.br','helessandro.trintinalio@brasfertil.agr.br','bruno@brumagran.com.br','suprimentos@cimol.ind.br','Henrique@docelarmoveis.com.br','Mauricio@dyplast.com.br','adrianedias@forteboi.ind.br','tonigriczi@yahoo.com.br','evandro@helaticinios.com.br','fabio@imarcal.com.br','adm@imopel.com.br','anderson@mocal.com.br','miguel.mana@outlook.com','diretor@mgmmoveis.com.br','anderson@mocal.com.br','geraldoferreira@montenegromadeiras.com.br','junior@nutridani.com.br','volkberger@hotmail.com','gustavo@pananmoveis.com.br','jocimarpazini@gmail.com','kerliton@permobili.com.br','lucio@moveisperoba.com.br','sergio@policast.com.br','romulo.favalessa@rimo.com.br','erasmo@aurorashopping.com.br','eutemar@bramagran.com.br','contabil@mcapixaba.com.br','calvigranitos@hotmail.com','tiago@carone.com.br','embragram@hotmail.com','eduardo.oliveira@fertgrow.com.br','gpgranitos@gpgranitos.com.br','gramalto@uol.com.br','rafael@gramarcal.com.br','rafael@gramarcal.com.br','eutemar@bramagran.com.br','Guilherme.Santos@zaffari.com.br','maurício@itapoama.com.br','marcelobruzzi@lakagranitos.com.br','madeval@bol.com.br','custo@mg2granitos.com.br','contabil@mcapixaba.com.br','jacqueline@grupoprogramar.com.br','milton@moinhocidadebella.com.br','jayme.soares@nipponflex.com.br','bisbach@hotmail.com','thiago@pedradofrade.com.br','jacqueline@grupoprogramar.com.br','fernando.marin@proteinorte.com.br','resimad@uol.com.br','marcus.capobianco@sbchemicals.com.br','marcos@santonio.com.br','marcus.capobianco@sbchemicals.com.br','jayme.soares@nipponflex.com.br','tom@villonialimentos.com.br','phillipyc@argalit.com.br','miriam@artsulgranitos.com.br','bebetobegran@gmail.com','ellon@bonardiquimica.com.br','leandro@mottinpavin.com.br','calimanltda@gmail.com','neyrosas@calponta.com.br','edufrazatto@hotmail.com','lufrazatto@hotmail.com','serginho@frigorificobolson.com.br','leonardo@gdindustria.ind.br','thiago@gelaboca.com.br','granitossantoandre@hotmail.com','cintia@kakagranitos.com.br','fabricio@montenegrogranitos.com.br','contato@naturalrocha.com.br','eduardo@portiforpedras.com.br','pedreirarioquati@gmail.com','millanamf@hotmail.com','bruno.carmo@bapka.com.br','gerentedeplanta@sorvetesguri.com.br','valeria@tecnogran.com.br','ptomedi@grupokymera.com.br','lucimar@superpuppo.com.br','ronaldo@topplastic.ind.br','fabricio@montenegrogranitos.com.br','pablo@valparaisoacquapark.com.br','fiscal@cristalforte.com','guilhermedevens@supermercadosdevens.com.br','rodrigo.pontes@nortecquimica.com.br','denilson@ceramitek.com.br','claudio@santacasapg.com','samadeiras4@gmail.com','direcao@granfortexport.com.br','antonia.araujo@e-copi.com.br','operacional@vivazcataratas.com.br','eliandro_c@hotmail.com','nathan.caprigran@outlook.com','michel@magnitos.com.br','adv.eduardomanica@gmail.com','claudio.melo@tecnosulfur.com.br','gerencia@mineracaoipiranga.com','andriw_mocellin@hotmail.com','rpaduani@almavivadobrasil.com.br','samantha.sathler@cimentonacional.com.br','samantha.sathler@cimentonacional.com.br','adeildo.souza@pbastones.com.br'];
        for($i = 0; $i < sizeof($arrayEmail); $i++) {
            $user = User::where('client_id', $arrayCodCliente[$i])->first();
            if(empty($user)) {
                echo 'VAZIO'.PHP_EOL;
            }else{
                echo 'encontrado'.PHP_EOL;
            }
        }
    }
}
