<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use DateTime;
use Exception;

class AtomDataExtractionService
{
    // -------------------------------------------------------------------------
    // Mapeos de códigos
    // -------------------------------------------------------------------------

    private const ESTADO_MAPPING = [
        'PRE' => 'ANUNCIO PREVIO',
        'PUB' => 'EN PLAZO',
        'EV'  => 'PENDIENTE DE ADJUDICACION',
        'ADJ' => 'ADJUDICADA',
        'RES' => 'RESUELTA',
        'ANU' => 'ANULADA',
        'ARC' => 'ARCHIVADA',
    ];

    private const TIPO_CONTRATO_MAPPING = [
        '1'   => 'SUMINISTROS',
        '2'   => 'SERVICIOS',
        '3'   => 'OBRAS',
        '21'  => 'CONCESIÓN DE OBRAS',
        '22'  => 'CONCESIÓN DE SERVICIOS',
        '31'  => 'PRIVADO',
        '32'  => 'PATRIMONIAL',
        '40'  => 'SUBVENCIÓN',
        '999' => 'SIN CLASIFICAR',
    ];

    private const PROCEDIMIENTO_MAPPING = [
        '1'  => 'Abierto',
        '2'  => 'Restringido',
        '3'  => 'Negociado con publicidad',
        '4'  => 'Diálogo competitivo',
        '5'  => 'Negociado sin publicidad',
        '6'  => 'Licitación con negociación',
        '7'  => 'Asociación para la innovación',
        '8'  => 'Simplificado',
        '9'  => 'Basado en acuerdo marco',
        '10' => 'Orden basada en sistema dinámico',
    ];

    private const URGENCIA_MAPPING = [
        '1' => 'Ordinaria',
        '2' => 'Urgente',
        '3' => 'Emergencia',
    ];

    private const METODO_PRESENTACION_MAPPING = [
        '1' => 'Electrónica',
        '2' => 'Correo',
        '3' => 'Presencial',
    ];

    private const SISTEMA_CONTRATACION_MAPPING = [
        '0' => 'Ninguno',
        '1' => 'Acuerdo Marco',
        '2' => 'Sistema Dinámico de Adquisición',
        '3' => 'Central de Compras',
    ];

    /**
     * CPV permitidos — como strings para comparación estricta.
     * Incluye equipos informáticos, redes, software e instalaciones TIC.
     */
    private const CPV_PERMITIDOS = [
        '30000000',
        '30100000',
        '30120000',
        '30121100',
        '30123000',
        '30123100',
        '30140000',
        '30141000',
        '30141100',
        '30141200',
        '30141300',
        '30150000',
        '30151000',
        '30170000',
        '30172000',
        '30190000',
        '30191000',
        '30191400',
        '30192000',
        '30192100',
        '30192200',
        '30192300',
        '30192400',
        '30192500',
        '30192600',
        '30192700',
        '30192800',
        '30192900',
        '30193000',
        '30193100',
        '30193200',
        '30197000',
        '30197100',
        '30197200',
        '30197300',
        '30197400',
        '30197500',
        '30197600',
        '30199000',
        '30199100',
        '30199200',
        '30199300',
        '30199400',
        '30199500',
        '30199600',
        '30199700',
        '30200000',
        '30210000',
        '30211000',
        '30211100',
        '30211200',
        '30211300',
        '30211400',
        '30211500',
        '30212000',
        '30212100',
        '30213000',
        '30213100',
        '30213200',
        '30213300',
        '30213400',
        '30213500',
        '30214000',
        '30215000',
        '30215100',
        '30216000',
        '30216100',
        '30230000',
        '30231000',
        '30231100',
        '30231200',
        '30231300',
        '30232000',
        '30232100',
        '30233000',
        '30233100',
        '30233300',
        '30234000',
        '30234100',
        '30234200',
        '30234300',
        '30234400',
        '30234500',
        '30234600',
        '30234700',
        '30236000',
        '30236100',
        '30236200',
        '30237000',
        '30237100',
        '30237200',
        '30237300',
        '30237400',
        '32000000',
        '32200000',
        '32250000',
        '32251000',
        '32251100',
        '32252000',
        '32252100',
        '32300000',
        '32320000',
        '32340000',
        '32341000',
        '32342000',
        '32342100',
        '32342200',
        '32342300',
        '32400000',
        '32410000',
        '32420000',
        '32421000',
        '32422000',
        '32423000',
        '32424000',
        '48000000',
        '51000000',
        '51600000',
        '51610000',
        '51611000',
        '51611100',
        '51612000',
        '72000000',
        '72100000',
        '72200000',
        '72400000',
        '72500000',
        '72600000',
        '72700000',
        '72900000',
    ];

    // -------------------------------------------------------------------------
    // Punto de entrada público
    // -------------------------------------------------------------------------

    /**
     * Procesa un archivo .atom (tanto licitacionesPerfilesContratanteCompleto3
     * como PlataformasAgregadasSinMenores) y persiste los registros en BBDD.
     */
    public function processAndSave(string $fileName): array
    {
        try {
            if (!Storage::disk('files')->exists($fileName)) {
                return ['success' => false, 'message' => "El archivo $fileName no existe en el disco."];
            }

            $xmlContent = Storage::disk('files')->get($fileName);
            $xml = new \SimpleXMLElement($xmlContent);

            // getNamespaces(true) recorre el árbol completo y devuelve TODOS los prefijos
            $ns = $xml->getNamespaces(true);

            // Intentar con namespace atom explícito si el acceso directo no da entries
            $entries = $xml->entry;
            if (empty($entries) || count($entries) === 0) {
                $atomNs = $ns['atom'] ?? 'http://www.w3.org/2005/Atom';
                $entries = $xml->children($atomNs)->entry;
            }

            $data          = [];
            $totalEntradas = 0;
            $sinCpv        = 0;
            $omitidos      = 0;
            $primerError   = null;

            foreach ($entries as $entry) {
                $totalEntradas++;
                try {
                    $extracted = $this->extractEntry($entry, $ns);
                    if ($extracted !== null) {
                        $data[] = $extracted;
                    } else {
                        $sinCpv++;
                    }
                } catch (\Throwable $e) {
                    $omitidos++;
                    if ($primerError === null) {
                        $primerError = $e->getMessage() . ' en ' . basename($e->getFile()) . ':' . $e->getLine();
                    }
                }
            }

            $registrosNuevos      = 0;
            $registrosActualizados = 0;

            foreach ($data as $projectData) {
                $existing = Project::where('expediente', $projectData['expediente'])->first();
                if ($existing) {
                    $existing->update($projectData);
                    $registrosActualizados++;
                } else {
                    Project::create($projectData);
                    $registrosNuevos++;
                }
            }

            $msg = "Proceso completado. Entradas en el XML: $totalEntradas | "
                 . "Sin CPV TIC: $sinCpv | Con errores: $omitidos | "
                 . "Nuevos: $registrosNuevos | Actualizados: $registrosActualizados.";
            if ($primerError !== null) {
                $msg .= " | Error: $primerError";
            }

            return ['success' => true, 'message' => $msg];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error procesando el XML o guardando en base de datos: ' . $e->getMessage(),
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Extracción de una entrada
    // -------------------------------------------------------------------------

    /**
     * Extrae todos los campos de un <entry> y devuelve null si ningún CPV
     * coincide con la lista permitida (evita insertar registros irrelevantes).
     */
    private function extractEntry(\SimpleXMLElement $entry, array $ns): ?array
    {
        // Registrar namespaces para XPath (necesario para llamadas posteriores)
        foreach ($ns as $prefix => $uri) {
            $entry->registerXPathNamespace($prefix, $uri);
        }

        $cfs     = $entry->children($ns['cac-place-ext'])->ContractFolderStatus;
        $project = $cfs->children($ns['cac'])->ProcurementProject;

        // --- CPV: comparación estricta string vs string ---
        $cpvCodes   = $this->extractCpvCodes($project, $ns);
        $cpvMatched = array_values(array_filter($cpvCodes, fn($c) => in_array($c, self::CPV_PERMITIDOS, true)));
        if (empty($cpvMatched)) {
            return null;
        }

        // --- Campos básicos del entry ---
        $link        = $this->getSafeNode($entry, 'link', 'href');
        $summary     = (string) $entry->summary;
        $fechaUpdate = $this->formatDate((string) $entry->updated);

        // --- Estado ---
        $estadoCod = (string) $cfs->children($ns['cbc-place-ext'] ?? '')->ContractFolderStatusCode;
        $estado    = self::ESTADO_MAPPING[$estadoCod] ?? 'DESCONOCIDO';

        // --- Expediente y objeto del contrato ---
        $expediente      = (string) $cfs->children($ns['cbc'])->ContractFolderID;
        $objetoContrato  = (string) $project->children($ns['cbc'])->Name;

        // --- Tipo y subtipo de contrato ---
        $tipoCod       = (string) $project->children($ns['cbc'])->TypeCode;
        $tipoContrato  = self::TIPO_CONTRATO_MAPPING[$tipoCod] ?? ($tipoCod ?: null);
        $subtipoCod    = (string) $project->children($ns['cbc'])->SubTypeCode;
        $subtipoContrato = $subtipoCod ?: null;

        // --- Importes ---
        $budget             = $project->children($ns['cac'])->BudgetAmount;
        $valorEstimado      = $this->parseAmount((string) $budget->children($ns['cbc'])->EstimatedOverallContractAmount);
        $importeSinIva      = $this->parseAmount((string) $budget->children($ns['cbc'])->TaxExclusiveAmount);
        // TotalAmount es el importe con IVA (TaxInclusiveAmount no existe en este esquema)
        $importeConIva      = $this->parseAmount((string) $budget->children($ns['cbc'])->TotalAmount);

        // --- Duración ---
        $plannedPeriod   = $project->children($ns['cac'] ?? '')->PlannedPeriod;
        $duracionNode    = $plannedPeriod ? $plannedPeriod->children($ns['cbc'] ?? '')->DurationMeasure : null;
        $duracionContrato = $duracionNode ? ((string) $duracionNode ?: null) : null;
        $unidadDuracion  = ($duracionNode && isset($duracionNode['unitCode'])) ? (string) $duracionNode['unitCode'] : null; // ANN | MON | DAY

        // --- Lugar de ejecución: RealizedLocation del proyecto (no dirección del órgano) ---
        $lugarEjecucion = $this->extractLugarEjecucion($project, $ns);
        $codigoNuts     = $this->extractNuts($project, $ns);

        // --- Órgano de contratación ---
        $lcp   = $cfs->children($ns['cac-place-ext'])->LocatedContractingParty;
        $party = $lcp->children($ns['cac'])->Party;

        $organoContratacion = (string) $party->children($ns['cac'])->PartyName->children($ns['cbc'])->Name
            ?: 'dato no disponible';

        // Identificaciones: iteramos todos los PartyIdentification para separar DIR3, NIF, ID_OC_PLAT...
        $ids             = $this->extractPartyIdentifications($party, $ns);
        $idOrgano        = $ids['dir3'] ?? $ids['id_oc_plat'] ?? $ids['id_plataforma'] ?? null;
        $nifOrgano       = $ids['nif'] ?? null;

        // Tipo de administración (solo presente en Completo3)
        $tipoAdministracion = (string) $lcp->children($ns['cbc'])->ContractingPartyTypeCode ?: null;

        // Enlace perfil contratante: BuyerProfileURIID > WebsiteURI > link de la entrada
        $enlacePerfil = (string) $lcp->children($ns['cbc'])->BuyerProfileURIID
            ?: (string) $party->children($ns['cbc'])->WebsiteURI
            ?: $link;

        // Plataforma de origen: AgentParty presente en PlataformasAgregadas, ausente en Completo3
        $agentParty = $party->children($ns['cac'] ?? '')->AgentParty;
        $agentPartyName = $agentParty ? $agentParty->children($ns['cac'] ?? '')->PartyName : null;
        $agentName = $agentPartyName ? (string) $agentPartyName->children($ns['cbc'] ?? '')->Name : '';
        $plataformaOrigen = $agentName ?: 'PLACSP';

        // --- TenderingTerms ---
        $terms = $cfs->children($ns['cac'])->TenderingTerms;

        $financiacionEuropea    = $this->extractFundingProgramCodes($terms, $ns);
        $descripcionFinanciacion = (string) $terms->children($ns['cbc'])->FundingProgram ?: null;
        $legDocRef           = $terms->children($ns['cac'] ?? '')->ProcurementLegislationDocumentReference ?? null;
        $legDocRefCbc        = $legDocRef ? $legDocRef->children($ns['cbc'] ?? '') : null;
        $directivaAplicacion = $legDocRefCbc ? ((string) $legDocRefCbc->ID ?: null) : null;

        $urlPpt = $this->extractTechnicalDocumentUri($terms, $ns);

        // --- TenderingProcess ---
        $process = $cfs->children($ns['cac'])->TenderingProcess;

        $procedimientoCod  = (string) $process->children($ns['cbc'])->ProcedureCode;
        $procedimiento     = self::PROCEDIMIENTO_MAPPING[$procedimientoCod] ?? ($procedimientoCod ?: null);

        $urgenciaCod  = (string) $process->children($ns['cbc'])->UrgencyCode;
        $tramitacion  = self::URGENCIA_MAPPING[$urgenciaCod] ?? ($urgenciaCod ?: null);

        $sistemaCod         = (string) $process->children($ns['cbc'])->ContractingSystemCode;
        $sistemaContratacion = self::SISTEMA_CONTRATACION_MAPPING[$sistemaCod] ?? null;

        $metodoCod         = (string) $process->children($ns['cbc'])->SubmissionMethodCode;
        $formaPresentacion = self::METODO_PRESENTACION_MAPPING[$metodoCod] ?? ($metodoCod ?: null);

        $sobreUmbralRaw = (string) $process->children($ns['cbc'])->OverThresholdIndicator;
        $sobreUmbral    = $sobreUmbralRaw !== '' ? filter_var($sobreUmbralRaw, FILTER_VALIDATE_BOOLEAN) : null;

        // Fechas del proceso
        $deadlinePeriod          = $process->children($ns['cac'] ?? '')->TenderSubmissionDeadlinePeriod ?? null;
        $deadlineCbc             = $deadlinePeriod ? $deadlinePeriod->children($ns['cbc'] ?? '') : null;
        $fechaMaximaPresentacion = $deadlineCbc ? ((string) $deadlineCbc->EndDate ?: null) : null;

        $availabilityPeriod = $process->children($ns['cac'] ?? '')->DocumentAvailabilityPeriod ?? null;
        $availabilityCbc    = $availabilityPeriod ? $availabilityPeriod->children($ns['cbc'] ?? '') : null;
        $fechaSolicitud     = $availabilityCbc ? ((string) $availabilityCbc->EndDate ?: null) : null;

        // --- Fecha de publicación (primera ValidNoticeInfo de tipo DOC_CN o DOC_CD) ---
        $fechaPublicacion = $this->extractFechaPublicacion($cfs, $ns);

        // --- Resultado de adjudicación (solo en Completo3, ausente en PlataformasAgregadas) ---
        $adjudicacion = $this->extractTenderResult($cfs, $ns);

        return [
            // Campos existentes en el modelo
            'expediente'                  => $expediente,
            'link'                        => $link,
            'sumario'                     => $summary,
            'fecha_updated'               => $fechaUpdate,
            'fecha_publicacion'           => $fechaPublicacion,
            'fecha_presentacion'          => $fechaMaximaPresentacion,
            'vigente_anulada_archivada'   => match($estadoCod) {
                'ANU'          => 'ANULADA',
                'ARC'          => 'ARCHIVADA',
                default        => 'VIGENTE',
            },
            'estado'                      => $estado,
            'organo_contratacion'         => $organoContratacion,
            'objeto_contratacion'         => $objetoContrato,
            'lugar_ejecucion'             => $lugarEjecucion,
            'presupuesto_sin_impuestos'   => $importeSinIva,
            'presupuesto_con_impuestos'   => $importeConIva,
            'tipo_contrato'               => $tipoContrato,
            'id_organo_contratacion'      => $idOrgano,
            'nif_organo_contratacion'     => $nifOrgano,
            'enlace_perfil_contratante'   => $enlacePerfil,
            'tipo_administracion'         => $tipoAdministracion,
            'sistema_contratacion'        => $sistemaContratacion,
            'tramitacion'                 => $tramitacion,
            'forma_presentacion'          => $formaPresentacion,
            'fecha_solicitud'             => $fechaSolicitud,
            'directiva_aplicacion'        => $directivaAplicacion,
            'financiacion_europea'        => $financiacionEuropea,
            'descripcion_financiacion'    => $descripcionFinanciacion,
            'url_ppt'                     => $urlPpt,
            'subcontratacion_permitido'   => null, // No disponible en el esquema CODICE
            'subcontratacion_porcentaje'  => null,
            // Campos nuevos — añadir a la migración si no existen
            'valor_estimado_total'        => $valorEstimado,
            'subtipo_contrato'            => $subtipoContrato,
            'duracion_contrato'           => $duracionContrato,
            'unidad_duracion'             => $unidadDuracion,     // ANN | MON | DAY
            'codigo_nuts'                 => $codigoNuts,
            'procedimiento'               => $procedimiento,
            'sobre_umbral'                => $sobreUmbral,
            'cpv'                         => json_encode($cpvMatched),
            'plataforma_origen'           => $plataformaOrigen,
            // Adjudicación (null cuando el contrato aún no está adjudicado)
            'fecha_adjudicacion'          => $adjudicacion['fecha_adjudicacion'],
            'empresa_adjudicataria'       => $adjudicacion['empresa'],
            'nif_adjudicatario'           => $adjudicacion['nif'],
            'importe_adjudicacion_sin_iva' => $adjudicacion['importe_sin_iva'],
            'importe_adjudicacion_con_iva' => $adjudicacion['importe_con_iva'],
            'num_ofertas'                 => $adjudicacion['num_ofertas'],
            'num_ofertas_pyme'            => $adjudicacion['num_ofertas_pyme'],
            'adjudicado_a_pyme'           => $adjudicacion['pyme'],
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers de extracción
    // -------------------------------------------------------------------------

    /**
     * Devuelve todos los CPV del proyecto como array de strings de 8 dígitos.
     * Itera todos los nodos RequiredCommodityClassification (puede haber varios).
     */
    private function extractCpvCodes(\SimpleXMLElement $project, array $ns): array
    {
        $codes = [];
        foreach ($project->children($ns['cac'] ?? '') as $nodeName => $node) {
            if ($nodeName !== 'RequiredCommodityClassification') {
                continue;
            }
            $cbcNode = $node->children($ns['cbc'] ?? '');
            $code    = $cbcNode ? (string) $cbcNode->ItemClassificationCode : '';
            if ($code !== '') {
                $codes[] = $code;
            }
        }
        return $codes;
    }

    /**
     * Itera todos los PartyIdentification y los indexa por schemeName en minúsculas.
     * Claves posibles: 'dir3', 'nif', 'id_plataforma', 'id_oc_plat'.
     */
    private function extractPartyIdentifications(\SimpleXMLElement $party, array $ns): array
    {
        $ids = [];
        foreach ($party->children($ns['cac'] ?? '') as $nodeName => $piNode) {
            if ($nodeName !== 'PartyIdentification') {
                continue;
            }
            $cbcChildren = $piNode->children($ns['cbc'] ?? '');
            if ($cbcChildren === null) {
                continue;
            }
            $idNode = $cbcChildren->ID;
            if ($idNode === null) {
                continue;
            }
            $scheme = strtolower((string) ($idNode->attributes()['schemeName'] ?? ''));
            $value  = (string) $idNode;
            if ($scheme !== '' && $value !== '') {
                // Normalizar 'id_plataforma' y 'id_oc_plat' que vienen como
                // 'ID_PLATAFORMA' e 'ID_OC_PLAT' en el XML
                $ids[str_replace('-', '_', strtolower($scheme))] = $value;
            }
        }
        return $ids;
    }

    /**
     * Lugar de ejecución desde RealizedLocation del proyecto.
     * Prioriza el nombre de provincia; si no existe, usa el código NUTS.
     */
    private function extractLugarEjecucion(\SimpleXMLElement $project, array $ns): string
    {
        $location    = $project->children($ns['cac'] ?? '')->RealizedLocation ?? null;
        $cbcLocation = $location ? $location->children($ns['cbc'] ?? '') : null;
        if (!$cbcLocation) {
            return 'dato no disponible';
        }
        $lugar = (string) $cbcLocation->CountrySubentity;
        return $lugar !== '' ? $lugar : ((string) $cbcLocation->CountrySubentityCode ?: 'dato no disponible');
    }

    /**
     * Código NUTS de la ubicación de ejecución.
     */
    private function extractNuts(\SimpleXMLElement $project, array $ns): ?string
    {
        $location    = $project->children($ns['cac'] ?? '')->RealizedLocation ?? null;
        $cbcLocation = $location ? $location->children($ns['cbc'] ?? '') : null;
        return $cbcLocation ? ((string) $cbcLocation->CountrySubentityCode ?: null) : null;
    }

    /**
     * Concatena todos los FundingProgramCode presentes (puede haber varios: EU, FEDER, etc.).
     * Itera todos los hijos cbc del nodo TenderingTerms para capturar repetidos.
     */
    private function extractFundingProgramCodes(\SimpleXMLElement $terms, array $ns): ?string
    {
        $codes       = [];
        $cbcChildren = $terms->children($ns['cbc'] ?? '');
        foreach (($cbcChildren ?? []) as $nodeName => $node) {
            if ($nodeName === 'FundingProgramCode') {
                $val = (string) $node;
                if ($val !== '') {
                    $codes[] = $val;
                }
            }
        }
        return $codes ? implode(', ', array_unique($codes)) : null;
    }

    /**
     * Extrae la URI del TechnicalDocumentReference (URL de descarga del PPT).
     * Ruta: TenderingTerms > cac:TechnicalDocumentReference > cac:Attachment
     *       > cac:ExternalReference > cbc:URI
     */
    private function extractTechnicalDocumentUri(\SimpleXMLElement $terms, array $ns): ?string
    {
        $techDocRef = $terms->children($ns['cac'] ?? '')->TechnicalDocumentReference ?? null;
        if (!$techDocRef) {
            return null;
        }
        $attachment = $techDocRef->children($ns['cac'] ?? '')->Attachment ?? null;
        if (!$attachment) {
            return null;
        }
        $extRef = $attachment->children($ns['cac'] ?? '')->ExternalReference ?? null;
        if (!$extRef) {
            return null;
        }
        $uri = (string) $extRef->children($ns['cbc'] ?? '')->URI;
        return $uri !== '' ? $uri : null;
    }

    /**
     * Busca la fecha de publicación en ValidNoticeInfo priorizando el tipo DOC_CN / DOC_CD.
     * Itera todos los nodos ValidNoticeInfo (puede haber varios por expediente).
     */
    private function extractFechaPublicacion(\SimpleXMLElement $cfs, array $ns): ?string
    {
        $fallback = null;

        foreach ($cfs->children($ns['cac-place-ext']) as $nodeName => $notice) {
            if ($nodeName !== 'ValidNoticeInfo') {
                continue;
            }

            $typeCode  = (string) $notice->children($ns['cbc-place-ext'] ?? '')->NoticeTypeCode;
            $pubStatus = $notice->children($ns['cac-place-ext'] ?? '')->AdditionalPublicationStatus ?? null;
            $docRef    = $pubStatus ? $pubStatus->children($ns['cac-place-ext'] ?? '')->AdditionalPublicationDocumentReference ?? null : null;
            $docRefCbc = $docRef ? $docRef->children($ns['cbc'] ?? '') : null;
            $date      = $docRefCbc ? (string) $docRefCbc->IssueDate : '';

            if ($date === '') {
                continue;
            }

            // Convocatoria original: devolvemos inmediatamente
            if (in_array($typeCode, ['DOC_CN', 'DOC_CD'], true)) {
                return $date;
            }

            // Guardamos el primero disponible como fallback
            if ($fallback === null) {
                $fallback = $date;
            }
        }

        return $fallback;
    }

    /**
     * Extrae los datos de adjudicación de TODOS los nodos TenderResult del contrato.
     * Un contrato con lotes tiene un TenderResult por lote; los importes se suman
     * y las empresas/NIFs se concatenan con ' / '.
     * En PlataformasAgregadas el contrato suele estar en estado EV y este nodo está
     * ausente; en ese caso todos los valores devueltos serán null.
     */
    private function extractTenderResult(\SimpleXMLElement $cfs, array $ns): array
    {
        $result = [
            'fecha_adjudicacion' => null,
            'empresa'            => null,
            'nif'                => null,
            'importe_sin_iva'    => null,
            'importe_con_iva'    => null,
            'num_ofertas'        => null,
            'num_ofertas_pyme'   => null,
            'pyme'               => null,
        ];

        $empresas      = [];
        $nifs          = [];
        $importeSinIva = 0.0;
        $importeConIva = 0.0;
        $hasImporteSin = false;
        $hasImporteCon = false;

        foreach ($cfs->children($ns['cac'] ?? '') as $nodeName => $tr) {
            if ($nodeName !== 'TenderResult') {
                continue;
            }

            $trCbc     = $tr->children($ns['cbc'] ?? '');
            $awardDate = (string) $trCbc->AwardDate;
            if ($awardDate === '') {
                continue;
            }

            // Fecha, cantidades y flag pyme: del primer TenderResult válido
            if ($result['fecha_adjudicacion'] === null) {
                $result['fecha_adjudicacion'] = $awardDate;

                $qty = (string) $trCbc->ReceivedTenderQuantity;
                $result['num_ofertas'] = is_numeric($qty) ? (int) $qty : null;

                $qtyPyme = (string) $trCbc->SMEsReceivedTenderQuantity;
                $result['num_ofertas_pyme'] = is_numeric($qtyPyme) ? (int) $qtyPyme : null;

                $pymeRaw = (string) $trCbc->SMEAwardedIndicator;
                $result['pyme'] = $pymeRaw !== '' ? filter_var($pymeRaw, FILTER_VALIDATE_BOOLEAN) : null;
            }

            // Iterar todas las WinningParty del lote (soporta UTE y multi-lote)
            foreach ($tr->children($ns['cac'] ?? '') as $wpName => $winningParty) {
                if ($wpName !== 'WinningParty') {
                    continue;
                }

                $empresa = (string) $winningParty
                    ->children($ns['cac'] ?? '')->PartyName
                    ->children($ns['cbc'] ?? '')->Name
                    ?: null;
                if ($empresa !== null) {
                    $empresas[] = $empresa;
                }

                // Buscar NIF entre los PartyIdentification
                $nifFound = null;
                foreach ($winningParty->children($ns['cac'] ?? '') as $piName => $piNode) {
                    if ($piName !== 'PartyIdentification') {
                        continue;
                    }
                    $idNode = $piNode->children($ns['cbc'] ?? '')->ID;
                    $scheme = strtolower((string) ($idNode->attributes()['schemeName'] ?? ''));
                    if ($scheme === 'nif') {
                        $nifFound = (string) $idNode;
                        break;
                    }
                }
                // Fallback: primer identificador disponible si no hay NIF explícito
                if ($nifFound === null) {
                    $nifFound = ((string) $winningParty
                        ->children($ns['cac'] ?? '')->PartyIdentification
                        ->children($ns['cbc'] ?? '')->ID) ?: null;
                }
                if ($nifFound !== null) {
                    $nifs[] = $nifFound;
                }
            }

            // Importes del lote — se acumulan si hay varios lotes
            $awardedProject = $tr->children($ns['cac'] ?? '')->AwardedTenderedProject;
            $legalMonetary  = $awardedProject ? $awardedProject->children($ns['cac'] ?? '')->LegalMonetaryTotal : null;

            if ($legalMonetary !== null && $legalMonetary->count() > 0) {
                $monetaryCbc = $legalMonetary->children($ns['cbc'] ?? '');

                $sin = $this->parseAmount((string) $monetaryCbc->TaxExclusiveAmount);
                $con = $this->parseAmount((string) $monetaryCbc->PayableAmount);

                if ($sin !== null) {
                    $importeSinIva += $sin;
                    $hasImporteSin  = true;
                }
                if ($con !== null) {
                    $importeConIva += $con;
                    $hasImporteCon  = true;
                }
            }
        }

        if ($result['fecha_adjudicacion'] === null) {
            return $result;
        }

        $result['empresa']         = $empresas ? implode(' / ', array_unique($empresas)) : null;
        $result['nif']             = $nifs     ? implode(' / ', array_unique($nifs))     : null;
        $result['importe_sin_iva'] = $hasImporteSin ? $importeSinIva : null;
        $result['importe_con_iva'] = $hasImporteCon ? $importeConIva : null;

        return $result;
    }

    // -------------------------------------------------------------------------
    // Utilidades
    // -------------------------------------------------------------------------

    private function parseAmount(string $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function formatDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }
        try {
            return (new DateTime($value))->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }

    private function getSafeNode(\SimpleXMLElement $entry, string $propertyName, ?string $attrName = null): string
    {
        if (!isset($entry->{$propertyName})) {
            return 'No disponible';
        }
        if ($attrName !== null) {
            return isset($entry->{$propertyName}[$attrName])
                ? (string) $entry->{$propertyName}[$attrName]
                : 'No disponible';
        }
        return (string) $entry->{$propertyName};
    }
}
