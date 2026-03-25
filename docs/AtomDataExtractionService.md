# AtomDataExtractionService — Cómo funciona

## ¿Qué hace este servicio?

Se encarga de **leer un archivo XML (.atom) de PLACSP** (la Plataforma de Contratación del Sector Público de España), filtrar las licitaciones que nos interesan (las de tecnología/TIC) y guardarlas en la base de datos.

---

## Flujo general (paso a paso)

```
Archivo .atom en disco
        ↓
  Leer y parsear XML
        ↓
  Recorrer cada <entry>
        ↓
  ¿Tiene algún código CPV de TIC? ──── NO ──→ Ignorar entrada
        ↓ SÍ
  Extraer todos los campos
        ↓
  ¿Ya existe en BD (mismo expediente)? ── SÍ ──→ Actualizar
        ↓ NO
  Crear nuevo registro
        ↓
  Devolver resumen: nuevos / actualizados / omitidos
```

---

## El método principal: `processAndSave()`

Es el único método público. Recibe el nombre del archivo y devuelve un array con:
- `success`: true/false
- `message`: resumen del resultado (cuántos creados, actualizados, errores)

**Ejemplo de resultado:**
```
Proceso completado. Entradas en el XML: 1500 | Sin CPV TIC: 1350 |
Con errores: 2 | Nuevos: 120 | Actualizados: 28.
```

---

## El filtro CPV — ¿Por qué solo guardamos algunas licitaciones?

El XML de PLACSP contiene **miles de contratos** de todo tipo (construcción, limpieza, catering, etc.). Solo nos interesan los de **tecnología e informática**.

Para eso existe la lista `CPV_PERMITIDOS`: son los códigos CPV (clasificación europea de productos y servicios) correspondientes a:

| Rango | Categoría |
|-------|-----------|
| `30xxxxxxx` | Equipos informáticos, impresoras, periféricos |
| `32xxxxxxx` | Equipos de comunicación y redes |
| `48xxxxxxx` | Software |
| `51600000+` | Instalación de equipos TIC |
| `72xxxxxxx` | Servicios informáticos |

Si una licitación **no tiene ningún CPV de esa lista**, se descarta sin guardar.

---

## Qué datos se extraen de cada licitación

Una vez que una entrada pasa el filtro CPV, se extrae toda esta información del XML:

### Identificación básica
| Campo | Qué es |
|-------|--------|
| `expediente` | Número de expediente (clave única) |
| `objeto_contratacion` | Nombre/descripción del contrato |
| `estado` | Estado actual (ver tabla abajo) |
| `link` | Enlace a la licitación |

### Estados posibles
| Código XML | Lo que guardamos |
|-----------|-----------------|
| PRE | ANUNCIO PREVIO |
| PUB | EN PLAZO |
| EV | PENDIENTE DE ADJUDICACION |
| ADJ | ADJUDICADA |
| RES | RESUELTA |
| ANU | ANULADA |
| ARC | ARCHIVADA |

### Importes
| Campo | Qué es |
|-------|--------|
| `presupuesto_sin_impuestos` | Presupuesto base sin IVA |
| `presupuesto_con_impuestos` | Presupuesto base con IVA |
| `valor_estimado_total` | Valor estimado total del contrato |

### Órgano contratante
| Campo | Qué es |
|-------|--------|
| `organo_contratacion` | Nombre del organismo que licita |
| `id_organo_contratacion` | Código DIR3 o identificador de plataforma |
| `nif_organo_contratacion` | NIF del organismo |
| `enlace_perfil_contratante` | URL del perfil del contratante |
| `tipo_administracion` | Tipo de administración pública |

### Características del contrato
| Campo | Qué es |
|-------|--------|
| `tipo_contrato` | Servicios / Suministros / Obras / etc. |
| `procedimiento` | Abierto / Restringido / Negociado / etc. |
| `tramitacion` | Ordinaria / Urgente / Emergencia |
| `forma_presentacion` | Electrónica / Correo / Presencial |
| `sistema_contratacion` | Ninguno / Acuerdo Marco / etc. |
| `duracion_contrato` | Duración numérica |
| `unidad_duracion` | ANN (años) / MON (meses) / DAY (días) |
| `sobre_umbral` | Si supera el umbral europeo (true/false) |

### Fechas
| Campo | Qué es |
|-------|--------|
| `fecha_publicacion` | Fecha en que se publicó |
| `fecha_presentacion` | Fecha límite para presentar ofertas |
| `fecha_solicitud` | Fecha límite para solicitar documentación |
| `fecha_updated` | Última actualización en PLACSP |

### CPV y plataforma
| Campo | Qué es |
|-------|--------|
| `cpv` | JSON con los códigos CPV que coincidieron |
| `plataforma_origen` | PLACSP o el nombre de la plataforma agregada |
| `lugar_ejecucion` | Provincia / comunidad autónoma |
| `codigo_nuts` | Código NUTS de la ubicación |

### Adjudicación (solo cuando el contrato ya está adjudicado)
| Campo | Qué es |
|-------|--------|
| `empresa_adjudicataria` | Nombre de la empresa ganadora |
| `nif_adjudicatario` | NIF de la empresa ganadora |
| `fecha_adjudicacion` | Fecha de adjudicación |
| `importe_adjudicacion_sin_iva` | Importe final sin IVA |
| `importe_adjudicacion_con_iva` | Importe final con IVA |
| `num_ofertas` | Cuántas empresas se presentaron |
| `num_ofertas_pyme` | Cuántas eran PYMEs |
| `adjudicado_a_pyme` | Si la ganadora es PYME (true/false) |

---

## Tipos de archivo .atom compatibles

El servicio soporta dos formatos que publica PLACSP:

1. **`licitacionesPerfilesContratanteCompleto3`** — El completo. Incluye datos de adjudicación, tipo de administración y más detalle.
2. **`PlataformasAgregadasSinMenores`** — Licitaciones de plataformas autonómicas agregadas. Tiene menos detalle y no suele incluir adjudicación.

El servicio detecta automáticamente cuál es por la presencia o ausencia de ciertos nodos en el XML.

---

## Gestión de duplicados

El campo `expediente` es la **clave única**. Cuando se procesa un archivo:

- Si el expediente **ya existe** en la base de datos → se **actualiza** con los nuevos datos
- Si el expediente **no existe** → se **crea** un nuevo registro

Esto permite reprocesar el mismo archivo o archivos más actualizados sin crear duplicados.

---

## ¿Qué pasa si hay un error en una entrada?

Si una entrada del XML tiene datos malformados o campos inesperados, **no se detiene todo el proceso**. El error se captura, esa entrada se cuenta como "omitida" y el proceso continúa con las demás. Al final se informa del primer error encontrado en el mensaje de resultado.

---

## Los namespaces XML

El formato ATOM de PLACSP usa múltiples namespaces XML (espacios de nombres). Son prefijos que identifican de qué "vocabulario" viene cada campo:

| Prefijo | Qué contiene |
|---------|-------------|
| `cbc` | Campos de texto simples (nombres, códigos, importes) |
| `cac` | Bloques estructurados (órgano, proceso, términos) |
| `cbc-place-ext` | Extensiones PLACSP para campos simples |
| `cac-place-ext` | Extensiones PLACSP para bloques estructurados |
| `atom` | La estructura base del feed ATOM |

El servicio detecta estos namespaces automáticamente con `getNamespaces(true)` para no depender de un formato fijo.
