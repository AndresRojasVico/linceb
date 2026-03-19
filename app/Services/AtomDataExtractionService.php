<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use DateTime;
use Exception;

class AtomDataExtractionService
{
    /**
     * Procesa y extrae datos de un archivo XML ATOM y los guarda en la base de datos.
     *
     * @param string $fileName
     * @return array
     */
    public function processAndSave(string $fileName): array
    {
        try {
            if (!Storage::disk('files')->exists($fileName)) {
                return ['success' => false, 'message' => "El archivo $fileName no existe en el disco."];
            }

            // Obtener el contenido del archivo
            $xmlContent = Storage::disk('files')->get($fileName);

            // Procesar el contenido del archivo XML
            $xml = new \SimpleXMLElement($xmlContent);

            // Inicializo datos
            $data = [];
            foreach ($xml->entry as $entry) {
                // Obtener los espacios de nombres
                $namespaces = $entry->getNamespaces(true);

                // Obtener los datos de la entrada (Sumario, Identificador, Link)
                $summary = (string) $entry->summary;

                $identificador = $entry->id;
                preg_match('/(\d+)$/', $identificador, $matches);
                // Si matchea agarra el 1, si no el raw id (protección anti-crash)
                $identificador = isset($matches[1]) ? $matches[1] : $identificador;

                $link = $this->getSafeNode($entry, 'link', 'href');

                // Fecha de actualización
                $fecha_update = (new DateTime($entry->updated))->format('Y-m-d H:i:s');

                // Estado
                $estado = (string) $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cbc-place-ext'])->ContractFolderStatusCode;
                $estadoMapping = [
                    'PRE' => 'ANUNCIO PREVIO',
                    'PUB' => 'EN PLAZO',
                    'EV' => 'PENDIENTE DE ADJUDICACION',
                    'ADJ' => 'ADJUDICADA',
                    'RES' => 'RESUELTA',
                    'ANU' => 'ANULADA',
                    'ARC' => 'ARCHIVADA',
                ];
                $estado = $estadoMapping[$estado] ?? 'DESCONOCIDO';

                // Número de expediente y Objeto del Contrato
                $contractFolderID = $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cbc'])->ContractFolderID;
                $objetoContrato = $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac'])->ProcurementProject->children($namespaces['cbc'])->Name;

                // Lugar de ejecución (Implementación Segura con XPath)
                $lugarEjecucion = "dato no disponible";
                $entry->registerXPathNamespace('cac-place-ext', $namespaces['cac-place-ext']);
                $entry->registerXPathNamespace('cac', $namespaces['cac']);
                $entry->registerXPathNamespace('cbc', $namespaces['cbc']);

                $xpath_query = './cac-place-ext:ContractFolderStatus/cac-place-ext:LocatedContractingParty/cac:Party/cac:PostalAddress/cbc:CityName';
                $nodos_encontrados = $entry->xpath($xpath_query);

                if (!empty($nodos_encontrados)) {
                    $lugarEjecucion = (string) $nodos_encontrados[0];
                }

                // Órgano de Contratación
                $organoContratacion = $entry
                    ->children($namespaces['cac-place-ext'])->ContractFolderStatus
                    ->children($namespaces['cac-place-ext'])->LocatedContractingParty
                    ->children($namespaces['cac'])->Party
                    ->children($namespaces['cac'])->PartyName
                    ->children($namespaces['cbc'])->Name ?? "dato no disponible";

                // Fechas (Asegurando null si vienen vacías)
                $fechaPuplicacion = (string) $entry
                    ->children($namespaces['cac-place-ext'])?->ContractFolderStatus
                        ?->children($namespaces['cac-place-ext'])?->ValidNoticeInfo
                        ?->children($namespaces['cac-place-ext'])?->AdditionalPublicationStatus
                        ?->children($namespaces['cac-place-ext'])?->AdditionalPublicationDocumentReference
                        ?->children($namespaces['cbc'])?->IssueDate;
                $fechaPuplicacion = $fechaPuplicacion ?: null;

                $fechaMaximaPresentacion = (string) $entry
                    ->children($namespaces['cac-place-ext'])?->ContractFolderStatus
                        ?->children($namespaces['cac'])?->TenderingProcess
                        ?->children($namespaces['cac'])?->TenderSubmissionDeadlinePeriod
                        ?->children($namespaces['cbc'])?->EndDate;
                $fechaMaximaPresentacion = $fechaMaximaPresentacion ?: null;

                // Importe (Sin y Con Impuestos)
                $importeSinImpuestos = (string) $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac'])->ProcurementProject->children($namespaces['cac'])->BudgetAmount->children($namespaces['cbc'])->TaxExclusiveAmount;
                $importeConImpuestos = (string) $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac'])->ProcurementProject->children($namespaces['cac'])->BudgetAmount->children($namespaces['cbc'])->TaxInclusiveAmount;
                $importeSinImpuestos = is_numeric($importeSinImpuestos) ? (float) $importeSinImpuestos : null;
                $importeConImpuestos = is_numeric($importeConImpuestos) ? (float) $importeConImpuestos : null;

                // Tipo de contrato (Suministros, Servicios, Obras, etc)
                $tipoContrato = (string) $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac'])->ProcurementProject->children($namespaces['cbc'])->TypeCode;
                $tipoContrato = $tipoContrato ?: null;
                
                // NIF / ID Órgano Contratación
                $idOrganoContratacion = null;
                $nifOrganoContratacion = null;
                $partyIdentifications = $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac-place-ext'])->LocatedContractingParty->children($namespaces['cac'])->Party->children($namespaces['cac'])->PartyIdentification;
                if ($partyIdentifications) {
                    $idOrganoContratacion = (string) $partyIdentifications->children($namespaces['cbc'])->ID ?: null;
                    $nifOrganoContratacion = $idOrganoContratacion; // Suele ser el mismo en PLACSP
                }

                // Enlace Perfil Contratante
                $enlacePerfil = null;
                $websiteURI = $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac-place-ext'])->LocatedContractingParty->children($namespaces['cac'])->Party->children($namespaces['cbc'])->WebsiteURI;
                if ($websiteURI) {
                    $enlacePerfil = (string) $websiteURI ?: null;
                } else {
                    $enlacePerfil = $link ?: null; // Fallback al enlace de la oferta
                }

                $tipoAdministracion = null;
                $sistemaContratacion = null;
                $tramitacion = null; 
                $formaPresentacion = null;
                $fechaSolicitud = null;
                $directivaAplicacion = null;
                $financiacionEuropea = null;
                $descripcionFinanciacion = null;
                $subcontratacionPermitido = null; 
                $subcontratacionPorcentaje = null;

                // Codigos
                $codes = [];
                $co = $entry->children($namespaces['cac-place-ext'])->ContractFolderStatus->children($namespaces['cac'])->ProcurementProject->children($namespaces['cac'])->RequiredCommodityClassification;
                if ($co) {
                    foreach ($co as $code) {
                        $codes[] = (string) $code->children($namespaces['cbc'])->ItemClassificationCode;
                    }
                }

                // Códigos permitidos filtrados (Obras y servicios informáticos)
                $codigos_permitidos = [
                    30000000,
                    30100000,
                    30120000,
                    30121100,
                    30123000,
                    30123100,
                    30140000,
                    30141000,
                    30141100,
                    30141200,
                    30141300,
                    30150000,
                    30151000,
                    30170000,
                    30172000,
                    30190000,
                    30191000,
                    30191400,
                    30192000,
                    30192100,
                    30192200,
                    30192300,
                    30192400,
                    30192500,
                    30192600,
                    30192700,
                    30192800,
                    30192900,
                    30193000,
                    30193100,
                    30193200,
                    30197000,
                    30197100,
                    30197200,
                    30197300,
                    30197400,
                    30197500,
                    30197600,
                    30199000,
                    30199100,
                    30199200,
                    30199300,
                    30199400,
                    30199500,
                    30199600,
                    30199700,
                    30200000,
                    30210000,
                    30211000,
                    30211100,
                    30211200,
                    30211300,
                    30211400,
                    30211500,
                    30212000,
                    30212100,
                    30213000,
                    30213100,
                    30213200,
                    30213300,
                    30213400,
                    30213500,
                    30214000,
                    30215000,
                    30215100,
                    30216000,
                    30216100,
                    30230000,
                    30231000,
                    30231100,
                    30231200,
                    30231300,
                    30232000,
                    30232100,
                    30233000,
                    30233100,
                    30233300,
                    30234000,
                    30234100,
                    30234200,
                    30234300,
                    30234400,
                    30234500,
                    30234600,
                    30234700,
                    30236000,
                    30236100,
                    30236200,
                    30237000,
                    30237100,
                    30237200,
                    30237300,
                    30237400,
                    32000000,
                    32200000,
                    32250000,
                    32251000,
                    32251100,
                    32252000,
                    32252100,
                    32300000,
                    32320000,
                    32340000,
                    32341000,
                    32342000,
                    32342100,
                    32342200,
                    32342300,
                    32400000,
                    32410000,
                    32420000,
                    32421000,
                    32422000,
                    32423000,
                    32424000,
                    48000000,
                    51000000,
                    51600000,
                    51610000,
                    51611000,
                    51611100,
                    51612000,
                    72000000,
                    72100000,
                    72200000,
                    72400000,
                    72500000,
                    72600000,
                    72700000,
                    72900000
                ];

                foreach ($codes as $code) {
                    if (in_array($code, $codigos_permitidos)) {
                        $data[] = [
                            'expediente' => (string) $contractFolderID,
                            'link' => (string) $link,
                            'sumario' => (string) $summary,
                            'fecha_updated' => $fecha_update,
                            'fecha_publicacion' => $fechaPuplicacion,
                            'fecha_presentacion' => $fechaMaximaPresentacion,
                            'vigente_anulada_archivada' => "desconocido",
                            'estado' => (string) $estado,
                            'organo_contratacion' => (string) $organoContratacion,
                            'objeto_contratacion' => (string) $objetoContrato,
                            'lugar_ejecucion' => (string) $lugarEjecucion,
                            // Nuevos campos mapeados debidamente para no dar error de sintaxis y respetar fillable
                            'presupuesto_sin_impuestos' => $importeSinImpuestos,
                            'presupuesto_con_impuestos' => $importeConImpuestos,
                            'tipo_contrato' => $tipoContrato,
                            'id_organo_contratacion' => $idOrganoContratacion,
                            'nif_organo_contratacion' => $nifOrganoContratacion,
                            'enlace_perfil_contratante' => $enlacePerfil,
                            'tipo_administracion' => $tipoAdministracion,
                            'sistema_contratacion' => $sistemaContratacion,
                            'tramitacion' => $tramitacion,
                            'forma_presentacion' => $formaPresentacion,
                            'fecha_solicitud' => $fechaSolicitud,
                            'directiva_aplicacion' => $directivaAplicacion,
                            'financiacion_europea' => $financiacionEuropea,
                            'descripcion_financiacion' => $descripcionFinanciacion,
                            'subcontratacion_permitido' => $subcontratacionPermitido,
                            'subcontratacion_porcentaje' => $subcontratacionPorcentaje,
                        ];
                        // Si ya coincide un código, evitamos meter el proyecto duplicado
                        break;
                    }
                }
            }

            // Guardado en la base de datos (Corrección de variables)
            $registrosNuevos = 0;
            $registrosActualizados = 0;

            foreach ($data as $projectData) {
                // Se busca el registro existente mediante su campo expediente
                $proyectoExistente = Project::where('expediente', $projectData['expediente'])->first();

                if ($proyectoExistente) {
                    $proyectoExistente->update($projectData);
                    $registrosActualizados++;
                } else {
                    Project::create($projectData);
                    $registrosNuevos++;
                }
            }

            return [
                'success' => true,
                'message' => "Base de datos actualizada correctamente. $registrosNuevos nuevos, $registrosActualizados actualizados."
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Ocurrió un error leyendo el XML o guardando en base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Helper paramétrica para evitar warnings al acceder a atributos link en caso de que fallen.
     */
    private function getSafeNode($entry, $propertyName, $attrName = null)
    {
        if (!isset($entry->{$propertyName})) {
            return "No disponible";
        }
        if ($attrName) {
            return isset($entry->{$propertyName}[$attrName]) ? (string) $entry->{$propertyName}[$attrName] : "No disponible";
        }
        return (string) $entry->{$propertyName};
    }
}
