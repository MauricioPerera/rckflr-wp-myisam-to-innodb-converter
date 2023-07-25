# WP MyISAM to InnoDB Converter

Este es un plugin de WordPress que permite a los usuarios convertir tablas de la base de datos de MyISAM a InnoDB. Los usuarios pueden seleccionar tablas específicas, respaldarlas y convertirlas a InnoDB. El plugin también proporciona una visualización del progreso de la conversión y bloquea la interacción del usuario durante el proceso para garantizar la integridad de los datos.

## Características

- Listado de todas las tablas de la base de datos que no están en formato InnoDB.
- Opción para seleccionar tablas específicas para la conversión.
- Creación de copias de seguridad de las tablas seleccionadas antes de la conversión.
- Conversión de tablas seleccionadas de MyISAM a InnoDB.
- Visualización del progreso de la conversión.
- Bloqueo de la interacción del usuario durante la conversión para garantizar la integridad de los datos.

## Instalación

1. Descarga el archivo ZIP del plugin y descomprímelo.
2. Sube la carpeta `wp-myisam-to-innodb-converter` a tu directorio `/wp-content/plugins/`.
3. Activa el plugin a través del menú 'Plugins' en WordPress.
4. Ve a la página de opciones del plugin (Configuración -> MyISAM to InnoDB Converter) para usarlo.

## Uso

1. Ve a la página de opciones del plugin (Configuración -> MyISAM to InnoDB Converter).
2. Verás una lista de todas las tablas de la base de datos que no están en formato InnoDB.
3. Selecciona las tablas que deseas convertir.
4. Haz clic en el botón 'Respaldar' para crear copias de seguridad de las tablas seleccionadas.
5. Haz clic en el botón 'Convertir a InnoDB' para convertir las tablas seleccionadas a InnoDB.

## Advertencias

- Asegúrate de que tu versión de MySQL admite InnoDB antes de usar este plugin.
- Este plugin no incluye ninguna validación de errores o manejo de excepciones. Asegúrate de tener copias de seguridad de tus datos antes de usarlo.
- Este plugin bloqueará la interacción del usuario durante la conversión para garantizar la integridad de los datos. No cierres la página o intentes hacer otra cosa durante la conversión.

## Soporte

Si tienes alguna pregunta o problema con el plugin, por favor, abre un issue en este repositorio.

## Donaciones

Si encuentras útil este plugin y quieres apoyar mi trabajo, puedes [comprarme un café](https://www.buymeacoffee.com/rckflr).
