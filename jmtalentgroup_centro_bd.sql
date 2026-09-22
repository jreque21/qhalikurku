-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-09-2026 a las 22:11:18
-- Versión del servidor: 11.4.13-MariaDB
-- Versión de PHP: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `jmtalentgroup_centro_bd`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_rolopcion` (IN `as_cod_rol` VARCHAR(20), IN `as_cod_usuario` VARCHAR(10))   BEGIN
  INSERT INTO MAE_ROL_OPCION(v_cod_rol, n_cod_opcion, v_flag_estado, v_aud_usr_reg, d_aud_fec_reg)
  SELECT as_cod_rol, t.n_cod_opcion, '0', as_cod_usuario, NOW() FROM MAE_MENU_OPCION t 
  WHERE not exists (SELECT * FROM MAE_ROL_OPCION x 
                    WHERE x.n_cod_opcion = t.n_cod_opcion and x.v_cod_rol = as_cod_rol);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_rolusuario` (IN `as_cod_rol` VARCHAR(20), IN `as_cod_tipouser` VARCHAR(10), IN `as_cod_usuario` VARCHAR(10))   BEGIN
  INSERT INTO MAE_ROL_USUARIO(v_cod_rol, v_cod_user, v_cod_tipouser, v_flag_estado, v_aud_usr_reg, d_aud_fec_reg)
  SELECT as_cod_rol, t.v_cod_user, t.v_cod_tipo, '0', as_cod_usuario, NOW() FROM MAE_USUARIO t 
  WHERE t.v_cod_tipo = as_cod_tipouser 
    AND not exists (SELECT * FROM MAE_ROL_USUARIO x 
                     WHERE x.v_cod_user = t.v_cod_user 
                       AND x.v_cod_tipouser = as_cod_tipouser
                       AND x.v_cod_rol = as_cod_rol
                       );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_sedeespecialista` (IN `an_cod_sede` INT(5), IN `as_cod_usuario` VARCHAR(10))   BEGIN
  INSERT INTO MAE_SEDE_ESPECIALISTA(n_cod_sede, n_cod_especialista, v_flag_estado, v_aud_usr_reg, d_aud_fec_reg)
  SELECT an_cod_sede, t.n_cod_especialista, '0', as_cod_usuario, NOW() 
  FROM MAE_ESPECIALISTA t 
  WHERE not exists (SELECT * FROM MAE_SEDE_ESPECIALISTA x 
                    WHERE x.n_cod_especialista = t.n_cod_especialista and x.n_cod_sede = an_cod_sede);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_generar_usuario` (IN `as_cod_tipouser` VARCHAR(10), IN `an_cod_referencia` INT(10), IN `as_clave` VARCHAR(60), IN `as_cod_usuario` VARCHAR(10))   BEGIN
  
  -- Declarar variables
  DECLARE li_cuenta int;
  DECLARE ls_nro_doc varchar(40);
  DECLARE ls_datos varchar(100);
  DECLARE ls_email varchar(40); 
  DECLARE ls_foto varchar(150); 
  
  -- Consultar si existe usuario (Evaluarlo)
  SELECT COUNT(1) INTO li_cuenta 
    FROM MAE_USUARIO u
   WHERE u.V_COD_TIPO = as_cod_tipouser
     AND u.N_COD_REFERENCIA = an_cod_referencia;
     
  -- No existe Usuario
  IF li_cuenta = 0 THEN
  	
    -- Capturar datos segun caso
    IF as_cod_tipouser = 'USER_PAC' THEN
      SELECT e.V_NRO_DOC, CONCAT_WS(' ', e.V_APE_PATERNO, e.V_APE_MATERNO, e.V_NOMBRES) as datos, e.V_EMAIL, e.V_FOTO 
        INTO ls_nro_doc, ls_datos, ls_email, ls_foto
        FROM MAE_PACIENTE e
       WHERE e.N_COD_PACIENTE = an_cod_referencia;
    ELSE
      SELECT i.V_NRO_DOC, CONCAT_WS(' ', i.V_APE_PATERNO, i.V_APE_MATERNO, i.V_NOMBRES) as datos, i.V_EMAIL, i.V_FOTO -- USER_ESP
        INTO ls_nro_doc, ls_datos, ls_email, ls_foto
        FROM MAE_ESPECIALISTA i
       WHERE i.N_COD_ESPECIALISTA = an_cod_referencia;
    END IF;

  	-- Insertar Usuario
      INSERT INTO MAE_USUARIO(v_cod_user, v_nombres, v_email, v_clave, v_foto, d_fec_alta, v_cod_tipo, n_cod_referencia, v_observacion, v_flag_estado, v_aud_usr_reg, d_aud_fec_reg)
      VALUES (ls_nro_doc, ls_datos, ls_email, as_clave, ls_foto, CURDATE(), as_cod_tipouser, an_cod_referencia, 'Generado automaticamente', '1', as_cod_usuario, NOW() );
   END IF;
  
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_CONFIG_WHATSAPP`
--

CREATE TABLE `MAE_CONFIG_WHATSAPP` (
  `V_ID` char(1) NOT NULL DEFAULT '1',
  `V_PHONE_NUMBER_ID` varchar(50) DEFAULT NULL,
  `V_ACCESS_TOKEN` varchar(1000) DEFAULT NULL,
  `V_NOMBRE_TEMPLATE` varchar(100) DEFAULT 'recordatorio_cita',
  `V_IDIOMA_TEMPLATE` varchar(10) DEFAULT 'es',
  `N_HORAS_ANTICIPACION` int(3) NOT NULL DEFAULT 24,
  `V_FLAG_ACTIVO` char(1) NOT NULL DEFAULT '0',
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_CONFIG_WHATSAPP`
--

INSERT INTO `MAE_CONFIG_WHATSAPP` (`V_ID`, `V_PHONE_NUMBER_ID`, `V_ACCESS_TOKEN`, `V_NOMBRE_TEMPLATE`, `V_IDIOMA_TEMPLATE`, `N_HORAS_ANTICIPACION`, `V_FLAG_ACTIVO`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('1', NULL, NULL, 'recordatorio_cita', 'es', 24, '0', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_DEPARTAMENTO`
--

CREATE TABLE `MAE_DEPARTAMENTO` (
  `N_COD_DEPARTAMENTO` int(5) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_PAIS` int(5) NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_DEPARTAMENTO`
--

INSERT INTO `MAE_DEPARTAMENTO` (`N_COD_DEPARTAMENTO`, `N_COD_PAIS`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 1, 'Amazonas', 'Amazonas', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00002, 1, 'Áncash', 'Áncash', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00003, 1, 'Apurímac', 'Apurímac', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00004, 1, 'Arequipa', 'Arequipa', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00005, 1, 'Ayacucho', 'Ayacucho', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00006, 1, 'Cajamarca', 'Cajamarca', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00007, 1, 'Callao', 'Callao', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00008, 1, 'Cusco', 'Cusco', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00009, 1, 'Huancavelica', 'Huancavelica', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00010, 1, 'Huánuco', 'Huánuco', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00011, 1, 'Ica', 'Ica', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00012, 1, 'Junín', 'Junín', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00013, 1, 'La Libertad', 'La Libertad', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00014, 1, 'Lambayeque', 'Lambayeque', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00015, 1, 'Lima', 'Lima', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00016, 1, 'Loreto', 'Loreto', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00017, 1, 'Madre de Dios', 'Madre de Dios', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00018, 1, 'Moquegua', 'Moquegua', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00019, 1, 'Pasco', 'Pasco', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00020, 1, 'Piura', 'Piura', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00021, 1, 'Puno', 'Puno', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00022, 1, 'San Martín', 'San Martín', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00023, 1, 'Tacna', 'Tacna', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00024, 1, 'Tumbes', 'Tumbes', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL),
(00025, 1, 'Ucayali', 'Ucayali', '1', 'admweb', '2026-09-12 09:11:42', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_DISTRITO`
--

CREATE TABLE `MAE_DISTRITO` (
  `N_COD_DISTRITO` int(5) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_PAIS` int(5) NOT NULL,
  `N_COD_DEPARTAMENTO` int(5) NOT NULL,
  `N_COD_PROVINCIA` int(5) DEFAULT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_DISTRITO`
--

INSERT INTO `MAE_DISTRITO` (`N_COD_DISTRITO`, `N_COD_PAIS`, `N_COD_DEPARTAMENTO`, `N_COD_PROVINCIA`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 1, 1, 1, 'CHACHAPOYAS', 'CHACHAPOYAS', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00002, 1, 1, 2, 'BAGUA', 'BAGUA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00003, 1, 1, 2, 'ARAMANGO', 'ARAMANGO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00004, 1, 2, 3, 'HUARAZ', 'HUARAZ', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00005, 1, 2, 4, 'CHIMBOTE', 'CHIMBOTE', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00006, 1, 2, 4, 'NUEVO CHIMBOTE', 'NUEVO CHIMBOTE', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00007, 1, 3, 5, 'ABANCAY', 'ABANCAY', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00008, 1, 4, 6, 'AREQUIPA', 'AREQUIPA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00009, 1, 5, 7, 'AYACUCHO', 'AYACUCHO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00010, 1, 6, 8, 'CAJAMARCA', 'CAJAMARCA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00011, 1, 7, 9, 'CALLAO', 'CALLAO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00012, 1, 8, 10, 'CUSCO', 'CUSCO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00013, 1, 9, 11, 'HUANCAVELICA', 'HUANCAVELICA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00014, 1, 10, 12, 'HUANUCO', 'HUANUCO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00015, 1, 11, 13, 'ICA', 'ICA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00016, 1, 12, 14, 'HUANCAYO', 'HUANCAYO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00017, 1, 13, 15, 'TRUJILLO', 'TRUJILLO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00018, 1, 14, 16, 'CHICLAYO', 'CHICLAYO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00019, 1, 15, 17, 'LIMA', 'LIMA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00020, 1, 15, 17, 'CARABAYLLO', 'CARABAYLLO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00021, 1, 15, 17, 'MIRAFLORES', 'MIRAFLORES', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00022, 1, 16, 18, 'IQUITOS', 'IQUITOS', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00023, 1, 17, 19, 'TAMBOPATA', 'TAMBOPATA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00024, 1, 18, 20, 'MOQUEGUA', 'MOQUEGUA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00025, 1, 19, 21, 'CHAUPIHUARANGA', 'CHAUPIHUARANGA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00026, 1, 20, 22, 'PIURA', 'PIURA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00027, 1, 21, 23, 'PUNO', 'PUNO', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00028, 1, 22, 24, 'MOYOBAMBA', 'MOYOBAMBA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00029, 1, 23, 25, 'TACNA', 'TACNA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00030, 1, 24, 26, 'TUMBES', 'TUMBES', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL),
(00031, 1, 25, 27, 'CALLERIA', 'CALLERIA', '1', 'admweb', '2026-09-12 09:19:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_EMPRESA`
--

CREATE TABLE `MAE_EMPRESA` (
  `V_ID` char(1) NOT NULL,
  `V_DESCRIPCION` varchar(250) DEFAULT NULL,
  `V_DIRECCION` varchar(200) DEFAULT NULL,
  `V_REFERENCIA` varchar(200) DEFAULT NULL,
  `V_RESPONSABLE` varchar(250) DEFAULT NULL,
  `V_LEMA` varchar(250) DEFAULT NULL,
  `V_EMAIL` varchar(40) DEFAULT NULL,
  `V_FONO` varchar(20) DEFAULT NULL,
  `V_MOVIL` varchar(20) DEFAULT NULL,
  `V_BIENVENIDA` text DEFAULT NULL,
  `V_SOMOS` text DEFAULT NULL,
  `V_MISION` text DEFAULT NULL,
  `V_VISION` text DEFAULT NULL,
  `V_URL_WEB` varchar(150) DEFAULT NULL,
  `V_URL_FB` varchar(150) DEFAULT NULL,
  `V_URL_YT` varchar(150) DEFAULT NULL,
  `V_URL_TW` varchar(150) DEFAULT NULL,
  `V_URL_IG` varchar(150) DEFAULT NULL,
  `V_URL_TK` varchar(150) DEFAULT NULL,
  `N_HORASXCLASE` int(5) DEFAULT NULL,
  `N_CLASESXHORARIO` int(5) DEFAULT NULL,
  `N_MIN_ESTUDIANTES` int(5) DEFAULT NULL,
  `N_MAX_ESTUDIANTES` int(5) DEFAULT NULL,
  `V_NOMBRE_SOPORTE` varchar(100) DEFAULT NULL,
  `V_EMAIL_SOPORTE` varchar(460) DEFAULT NULL,
  `V_FONO_SOPORTE` varchar(20) DEFAULT NULL,
  `V_CLAVE_MASTER` varchar(40) DEFAULT NULL,
  `V_FOTO` varchar(150) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_EMPRESA`
--

INSERT INTO `MAE_EMPRESA` (`V_ID`, `V_DESCRIPCION`, `V_DIRECCION`, `V_REFERENCIA`, `V_RESPONSABLE`, `V_LEMA`, `V_EMAIL`, `V_FONO`, `V_MOVIL`, `V_BIENVENIDA`, `V_SOMOS`, `V_MISION`, `V_VISION`, `V_URL_WEB`, `V_URL_FB`, `V_URL_YT`, `V_URL_TW`, `V_URL_IG`, `V_URL_TK`, `N_HORASXCLASE`, `N_CLASESXHORARIO`, `N_MIN_ESTUDIANTES`, `N_MAX_ESTUDIANTES`, `V_NOMBRE_SOPORTE`, `V_EMAIL_SOPORTE`, `V_FONO_SOPORTE`, `V_CLAVE_MASTER`, `V_FOTO`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('1', 'QHALI KURKU Centro de Terpia Fisica y Rehabilitacion', 'JR CHINCHA ALTA  394 Chachapoyas', '', 'John Monteza', 'Tu bienestar es nuestra mision', 'adm@jmtalentgroup.com', '', '987117717', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, '', 'adm@jmtalentgroup.com', '', NULL, 'LOGO-QHALI-KURKU.jpg', '1', 'admweb', '2026-03-14 11:01:52', 'admweb', '2026-09-16 06:29:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_ESPECIALIDAD`
--

CREATE TABLE `MAE_ESPECIALIDAD` (
  `N_COD_ESPECIALIDAD` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_ESPECIALIDAD`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_ESPECIALISTA`
--

CREATE TABLE `MAE_ESPECIALISTA` (
  `N_COD_ESPECIALISTA` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_NOMBRES` varchar(150) DEFAULT NULL,
  `V_APE_PATERNO` varchar(150) DEFAULT NULL,
  `V_APE_MATERNO` varchar(150) DEFAULT NULL,
  `V_FG_SEXO` varchar(1) DEFAULT NULL,
  `D_FEC_NACIMIENTO` date DEFAULT NULL,
  `N_COD_TIPODOC` int(5) DEFAULT NULL,
  `V_NRO_DOC` varchar(40) DEFAULT NULL,
  `N_COD_PAIS` int(5) DEFAULT NULL,
  `N_COD_DEPARTAMENTO` int(5) DEFAULT NULL,
  `N_COD_DISTRITO` int(5) DEFAULT NULL,
  `V_DIRECCION` varchar(200) DEFAULT NULL,
  `V_REFERENCIA` varchar(200) DEFAULT NULL,
  `V_EMAIL` varchar(40) DEFAULT NULL,
  `V_FONO` varchar(20) DEFAULT NULL,
  `V_MOVIL` varchar(20) DEFAULT NULL,
  `V_FOTO` varchar(150) DEFAULT NULL,
  `N_COD_ESPECIALIDAD` int(5) DEFAULT NULL,
  `V_COLEGIATURA` varchar(50) DEFAULT NULL,
  `D_FEC_ALTA` date DEFAULT NULL,
  `D_FEC_BAJA` date DEFAULT NULL,
  `V_MOTIVO_BAJA` varchar(800) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_ESPECIALISTA`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_FORMA_PAGO`
--

CREATE TABLE `MAE_FORMA_PAGO` (
  `N_COD_FORMAPAGO` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_FORMA_PAGO`
--

INSERT INTO `MAE_FORMA_PAGO` (`N_COD_FORMAPAGO`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 'Pago al Contado', 'Al Contado', '1', 'admweb', '2023-04-17 03:03:56', NULL, NULL),
(00002, 'Pago a Crédito', 'Al Crédito', '1', 'admweb', '2023-04-17 03:04:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_MEDIO_PAGO`
--

CREATE TABLE `MAE_MEDIO_PAGO` (
  `N_COD_MEDIOPAGO` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_MEDIO_PAGO`
--

INSERT INTO `MAE_MEDIO_PAGO` (`N_COD_MEDIOPAGO`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 'Efectivo', 'Efectivo', '1', 'admweb', '2023-04-17 03:05:09', NULL, NULL),
(00003, 'Tarjeta de Crédito o Débito', 'Tarjeta', '1', 'admweb', '2023-04-17 03:06:26', NULL, NULL),
(00004, 'Pagos Móviles', 'Pago Móvil', '1', 'admweb', '2023-04-17 03:06:54', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_MENU_NIVEL`
--

CREATE TABLE `MAE_MENU_NIVEL` (
  `N_COD_NIVEL` int(3) UNSIGNED ZEROFILL NOT NULL,
  `V_NOMBRE` varchar(150) NOT NULL,
  `V_ICONO` varchar(150) DEFAULT NULL,
  `N_ORDEN` int(3) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_MENU_NIVEL`
--

INSERT INTO `MAE_MENU_NIVEL` (`N_COD_NIVEL`, `V_NOMBRE`, `V_ICONO`, `N_ORDEN`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(001, 'Reportes', 'fa fa-dashboard', 1, '1', 'admweb', '2026-03-14 14:21:35', NULL, NULL),
(002, 'Administracion', 'fa fa-group', 2, '1', 'admweb', '2026-03-14 14:21:35', NULL, NULL),
(003, 'Centro', 'fa fa-thumb-tack', 6, '1', 'admweb', '2026-03-14 14:21:35', NULL, NULL),
(004, 'Maestras', 'fa fa-files-o', 7, '1', 'admweb', '2026-03-14 14:21:35', NULL, NULL),
(005, 'Seguridad', 'fa fa-lock', 8, '1', 'admweb', '2026-03-14 14:21:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_MENU_OPCION`
--

CREATE TABLE `MAE_MENU_OPCION` (
  `N_COD_OPCION` int(3) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_NIVEL` int(3) NOT NULL,
  `N_ORDEN` int(3) NOT NULL,
  `V_NOMBRE` varchar(150) DEFAULT NULL,
  `V_ETIQUETA` varchar(150) DEFAULT NULL,
  `V_URL` varchar(150) DEFAULT NULL,
  `V_ICONO` varchar(150) DEFAULT NULL,
  `V_COD_TABLA` varchar(150) DEFAULT NULL,
  `V_FLAG_PERMISO` char(1) NOT NULL DEFAULT '1',
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_MENU_OPCION`
--

INSERT INTO `MAE_MENU_OPCION` (`N_COD_OPCION`, `N_COD_NIVEL`, `N_ORDEN`, `V_NOMBRE`, `V_ETIQUETA`, `V_URL`, `V_ICONO`, `V_COD_TABLA`, `V_FLAG_PERMISO`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(001, 1, 1, 'Dashboard', 'Dashboard', 'panel_admin.php', 'fa fa-spinner', 'PANEL_ADMIN', '1', '1', 'admweb', '2026-03-14 14:21:51', NULL, NULL),
(002, 2, 1, 'Paciente', 'Paciente', 'mae_paciente_lista.php', 'fa fa-child', 'MAE_PACIENTE', '1', '1', 'admweb', '2026-03-14 15:31:25', NULL, NULL),
(003, 2, 2, 'Citas', 'CITA', 'mov_cita_lista.php', 'fa fa-calendar-check-o', 'MOV_CITA', '1', '1', 'admweb', '2026-03-14 15:31:25', NULL, NULL),
(004, 2, 3, 'Tratamientos', 'Tratamientos', 'mov_tratamiento_lista.php', 'fa fa-file-text-o', 'MOV_TRATAMIENTO', '1', '1', 'admweb', '2026-03-14 15:31:25', NULL, NULL),
(005, 2, 4, 'Pagos', 'Pagos', 'mov_pago_lista.php', 'fa fa-credit-card', 'MOV_PAGO', '1', '1', 'admweb', '2026-03-14 15:31:25', NULL, NULL),
(006, 3, 1, 'Empresa', 'Empresa', 'mae_empresa_editar.php', 'fa fa-sitemap', 'MAE_EMPRESA', '1', '1', 'admweb', '2026-03-14 15:31:49', NULL, NULL),
(007, 3, 2, 'Sedes', 'Sede', 'mae_sede_lista.php', 'fa fa-briefcase', 'MAE_SEDE', '1', '1', 'admweb', '2026-03-14 15:31:49', NULL, NULL),
(008, 3, 3, 'Especialista', 'Especialista', 'mae_especialista_lista.php', 'fa fa-user-secret', 'MAE_ESPECIALISTA', '1', '1', 'admweb', '2026-03-14 15:31:49', NULL, NULL),
(009, 3, 4, 'Sede - Especialista', 'Sede - Especialista', 'mae_sedeespecialista_lista.php', 'fa fa-slideshare', 'MAE_SEDE_ESPECIALISTA', '1', '1', 'admweb', '2026-03-14 15:31:49', NULL, NULL),
(010, 4, 1, 'Tipo de Documento', 'Tipo de Documento', 'mae_tipodoc_lista.php', 'fa fa-vcard-o', 'MAE_TIPO_DOC', '1', '1', 'admweb', '2026-03-14 15:32:03', NULL, NULL),
(011, 4, 2, 'Moneda', 'Moneda', 'mae_moneda_lista.php', 'fa fa-usd', 'MAE_MONEDA', '1', '1', 'admweb', '2026-03-14 15:32:03', NULL, NULL),
(012, 4, 3, 'Forma de Pago', 'Forma de Pago', 'mae_formapago_lista.php', 'fa fa-bitcoin', 'MAE_FORMA_PAGO', '1', '1', 'admweb', '2026-03-14 15:32:03', NULL, NULL),
(013, 4, 4, 'Medio de Pago', 'Medio de Pago', 'mae_mediopago_lista.php', 'fa fa-cc-visa', 'MAE_MEDIO_PAGO', '1', '1', 'admweb', '2026-03-14 15:32:03', NULL, NULL),
(014, 4, 5, 'Especialidad', 'Especialidad', 'mae_especialidad_lista.php', 'fa fa-diamond', 'MAE_ESPECIALIDAD', '1', '1', 'admweb', '2026-03-14 15:32:03', NULL, NULL),
(015, 4, 6, 'Tipos de Terapia', 'Tipos de Terapia', 'mae_tipoterapia_lista.php', 'fa fa-bell-o', 'MAE_TIPO_TERAPIA', '1', '1', 'admweb', '2026-03-14 15:32:03', NULL, NULL),
(016, 5, 1, 'Usuarios', 'Usuario', 'mae_usuario_lista.php', 'fa fa-user-o', 'MAE_USUARIO', '1', '1', 'admweb', '2026-03-14 15:32:25', NULL, NULL),
(017, 5, 2, 'Roles', 'Roles', 'mae_rol_lista.php', 'fa fa-briefcase', 'MAE_ROL', '1', '1', 'admweb', '2026-03-14 15:32:25', NULL, NULL),
(018, 5, 3, 'Rol - Usuario', 'Rol - Usuario', 'mae_rolusuario_lista.php', 'fa fa-cogs', 'MAE_ROL_USUARIO', '1', '1', 'admweb', '2026-03-14 15:32:25', NULL, NULL),
(019, 5, 4, 'Permisos', 'Permisos', 'mae_rolopcion_lista.php', 'fa fa-key', 'MAE_ROL_OPCION', '1', '1', 'admweb', '2026-03-14 15:32:25', NULL, NULL),
(020, 1, 2, 'Reportes', 'Reportes Principales', 'rpt_reportes_lista.php', 'fa fa-bar-chart', 'RPT_REPORTES', '1', '1', 'admweb', '2026-09-12 12:25:23', NULL, NULL),
(021, 1, 3, 'Ingresos por Periodo', 'Ingresos por Periodo', 'rpt_ingresos.php', 'fa fa-money', 'RPT_INGRESOS', '1', '1', 'admweb', '2026-09-12 12:25:23', NULL, NULL),
(022, 1, 4, 'Citas por Periodo', 'Citas por Periodo', 'rpt_citas.php', 'fa fa-calendar-check-o', 'RPT_CITAS', '1', '1', 'admweb', '2026-09-12 12:25:23', NULL, NULL),
(023, 1, 5, 'Productividad por Especialista', 'Productividad por Especialista', 'rpt_productividad.php', 'fa fa-user-md', 'RPT_PRODUCTIVIDAD', '1', '1', 'admweb', '2026-09-12 12:25:23', NULL, NULL),
(024, 1, 6, 'Cuentas por Cobrar', 'Cuentas por Cobrar', 'mov_comprobante_lista.php', 'fa fa-credit-card', 'MOV_COMPROBANTE', '1', '1', 'admweb', '2026-09-12 12:25:23', NULL, NULL),
(025, 3, 5, 'WhatsApp', 'Configuración de WhatsApp', 'mae_configwhatsapp_editar.php', 'fa fa-whatsapp', 'MAE_CONFIG_WHATSAPP', '1', '1', 'admweb', '2026-09-12 12:39:17', NULL, NULL),
(026, 2, 5, 'Historia Clínica', 'Historia Clínica', 'mov_historia_lista.php', 'fa fa-file-text-o', 'MOV_HISTORIA_CLINICA', '1', '1', 'admweb', '2026-09-12 12:59:56', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_MONEDA`
--

CREATE TABLE `MAE_MONEDA` (
  `N_COD_MONEDA` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_SIMBOLO` varchar(10) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_MONEDA`
--

INSERT INTO `MAE_MONEDA` (`N_COD_MONEDA`, `V_DES_LARGA`, `V_DES_CORTA`, `V_SIMBOLO`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 'SOL (PEN)', 'Soles', 'S/', '1', 'admweb', '2023-04-17 02:50:24', NULL, NULL),
(00002, 'Dólar (USD)', 'Dolares', '$/', '1', 'admweb', '2023-04-17 02:50:53', 'admweb', '2023-04-17 02:51:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_PACIENTE`
--

CREATE TABLE `MAE_PACIENTE` (
  `N_COD_PACIENTE` int(10) UNSIGNED ZEROFILL NOT NULL,
  `V_NOMBRES` varchar(150) DEFAULT NULL,
  `V_APE_PATERNO` varchar(150) DEFAULT NULL,
  `V_APE_MATERNO` varchar(150) DEFAULT NULL,
  `V_FG_SEXO` varchar(1) DEFAULT NULL,
  `D_FEC_NACIMIENTO` date DEFAULT NULL,
  `N_COD_TIPODOC` int(5) DEFAULT NULL,
  `V_NRO_DOC` varchar(40) DEFAULT NULL,
  `N_COD_PAIS` int(5) DEFAULT NULL,
  `N_COD_DEPARTAMENTO` int(5) DEFAULT NULL,
  `N_COD_PROVINCIA` int(5) DEFAULT NULL,
  `N_COD_DISTRITO` int(5) DEFAULT NULL,
  `V_DIRECCION` varchar(200) DEFAULT NULL,
  `V_REFERENCIA` varchar(200) DEFAULT NULL,
  `V_EMAIL` varchar(40) DEFAULT NULL,
  `V_FONO` varchar(20) DEFAULT NULL,
  `V_MOVIL` varchar(20) DEFAULT NULL,
  `V_FOTO` varchar(150) DEFAULT NULL,
  `D_FEC_ALTA` date DEFAULT NULL,
  `D_FEC_BAJA` date DEFAULT NULL,
  `V_MOTIVO_BAJA` varchar(800) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_PACIENTE`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_PAIS`
--

CREATE TABLE `MAE_PAIS` (
  `N_COD_PAIS` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_PAIS`
--

INSERT INTO `MAE_PAIS` (`N_COD_PAIS`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 'Perú', 'Perú', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00002, 'Venezuela', 'Venezuela', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00003, 'Argentina', 'Argentina', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00004, 'Bolivia', 'Bolivia', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00005, 'Brasil', 'Brasil', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00006, 'Chile', 'Chile', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00007, 'Colombia', 'Colombia', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00008, 'Ecuador', 'Ecuador', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00009, 'Paraguay', 'Paraguay', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL),
(00010, 'Uruguay', 'Uruguay', '1', 'admweb', '2026-03-14 15:33:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_PROVINCIA`
--

CREATE TABLE `MAE_PROVINCIA` (
  `N_COD_PROVINCIA` int(5) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_PAIS` int(5) NOT NULL,
  `N_COD_DEPARTAMENTO` int(5) NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_PROVINCIA`
--

INSERT INTO `MAE_PROVINCIA` (`N_COD_PROVINCIA`, `N_COD_PAIS`, `N_COD_DEPARTAMENTO`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 1, 1, 'Chachapoyas', 'Chachapoyas', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00002, 1, 1, 'Bagua', 'Bagua', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00003, 1, 2, 'Huaraz', 'Huaraz', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00004, 1, 2, 'Santa', 'Santa', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00005, 1, 3, 'Abancay', 'Abancay', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00006, 1, 4, 'Arequipa', 'Arequipa', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00007, 1, 5, 'Huamanga', 'Huamanga', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00008, 1, 6, 'Cajamarca', 'Cajamarca', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00009, 1, 7, 'Callao', 'Callao', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00010, 1, 8, 'Cusco', 'Cusco', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00011, 1, 9, 'Huancavelica', 'Huancavelica', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00012, 1, 10, 'Huánuco', 'Huánuco', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00013, 1, 11, 'Ica', 'Ica', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00014, 1, 12, 'Huancayo', 'Huancayo', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00015, 1, 13, 'Trujillo', 'Trujillo', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00016, 1, 14, 'Chiclayo', 'Chiclayo', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00017, 1, 15, 'Lima', 'Lima', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00018, 1, 16, 'Maynas', 'Maynas', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00019, 1, 17, 'Tambopata', 'Tambopata', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00020, 1, 18, 'Mariscal Nieto', 'Mariscal Nieto', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00021, 1, 19, 'Pasco', 'Pasco', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00022, 1, 20, 'Piura', 'Piura', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00023, 1, 21, 'Puno', 'Puno', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00024, 1, 22, 'Moyobamba', 'Moyobamba', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00025, 1, 23, 'Tacna', 'Tacna', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00026, 1, 24, 'Tumbes', 'Tumbes', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL),
(00027, 1, 25, 'Coronel Portillo', 'Coronel Portillo', '1', 'admweb', '2026-09-17 23:34:23', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_ROL`
--

CREATE TABLE `MAE_ROL` (
  `V_COD_ROL` varchar(20) NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_COD_TIPOUSER` varchar(10) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_ROL`
--

INSERT INTO `MAE_ROL` (`V_COD_ROL`, `V_DES_LARGA`, `V_DES_CORTA`, `V_COD_TIPOUSER`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('ESP_MED', 'Especialista Médico', 'Médico', 'USER_ESP', '1', 'admweb', '2026-03-17 08:06:16', NULL, NULL),
('WEB_MASTER', 'ADMINISTRADOR WEB', 'ADMIN. WEB', 'USER_ADM', '1', 'admweb', '2026-03-14 11:01:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_ROL_OPCION`
--

CREATE TABLE `MAE_ROL_OPCION` (
  `V_COD_ROL` varchar(20) NOT NULL,
  `N_COD_OPCION` int(3) UNSIGNED NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_ROL_OPCION`
--

INSERT INTO `MAE_ROL_OPCION` (`V_COD_ROL`, `N_COD_OPCION`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('ESP_MED', 1, '1', 'admweb', '2026-03-17 21:28:51', 'admweb', '2026-03-17 08:29:23'),
('ESP_MED', 2, '1', 'admweb', '2026-03-17 21:28:51', 'admweb', '2026-03-17 08:29:02'),
('ESP_MED', 3, '1', 'admweb', '2026-03-17 21:28:51', 'admweb', '2026-03-17 08:29:03'),
('ESP_MED', 4, '1', 'admweb', '2026-03-17 21:28:51', 'admweb', '2026-03-17 08:29:05'),
('ESP_MED', 5, '1', 'admweb', '2026-03-17 21:28:51', 'admweb', '2026-03-17 08:29:07'),
('ESP_MED', 6, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 7, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 8, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 9, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 10, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 11, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 12, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 13, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 14, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 15, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 16, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 17, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 18, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 19, '0', 'admweb', '2026-03-17 21:28:51', NULL, NULL),
('ESP_MED', 20, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('ESP_MED', 21, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('ESP_MED', 22, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('ESP_MED', 23, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('ESP_MED', 24, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('ESP_MED', 25, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('ESP_MED', 26, '0', 'admweb', '2026-09-13 21:13:09', NULL, NULL),
('WEB_MASTER', 1, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 2, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 3, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 4, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 5, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 6, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 7, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 8, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 9, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 10, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 11, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 12, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 13, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 14, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 15, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 16, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 17, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 18, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 19, '1', 'admweb', '2026-03-14 15:33:08', NULL, NULL),
('WEB_MASTER', 20, '1', 'admweb', '2026-09-12 12:27:08', 'admweb', '2026-09-12 11:27:14'),
('WEB_MASTER', 21, '1', 'admweb', '2026-09-12 12:27:08', 'admweb', '2026-09-12 11:27:14'),
('WEB_MASTER', 22, '1', 'admweb', '2026-09-12 12:27:08', 'admweb', '2026-09-12 11:27:15'),
('WEB_MASTER', 23, '1', 'admweb', '2026-09-12 12:27:08', 'admweb', '2026-09-12 11:27:16'),
('WEB_MASTER', 24, '1', 'admweb', '2026-09-12 12:27:08', 'admweb', '2026-09-12 11:27:18'),
('WEB_MASTER', 25, '1', 'admweb', '2026-09-12 12:43:28', 'admweb', '2026-09-12 11:43:31'),
('WEB_MASTER', 26, '1', 'admweb', '2026-09-13 21:36:50', 'admweb', '2026-09-13 08:36:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_ROL_USUARIO`
--

CREATE TABLE `MAE_ROL_USUARIO` (
  `V_COD_ROL` varchar(20) NOT NULL,
  `V_COD_USER` varchar(10) NOT NULL,
  `V_COD_TIPOUSER` varchar(10) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_ROL_USUARIO`
--

INSERT INTO `MAE_ROL_USUARIO` (`V_COD_ROL`, `V_COD_USER`, `V_COD_TIPOUSER`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('WEB_MASTER', 'admweb', 'USER_ADM', '1', 'admweb', '2026-03-14 11:01:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_SEDE`
--

CREATE TABLE `MAE_SEDE` (
  `N_COD_SEDE` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_NOMBRE` varchar(250) DEFAULT NULL,
  `N_COD_PAIS` int(5) DEFAULT NULL,
  `N_COD_DEPARTAMENTO` int(5) DEFAULT NULL,
  `N_COD_DISTRITO` int(5) DEFAULT NULL,
  `V_DIRECCION` varchar(500) DEFAULT NULL,
  `V_REFERENCIA` varchar(500) DEFAULT NULL,
  `V_EMAIL` varchar(80) DEFAULT NULL,
  `V_FONO` varchar(20) DEFAULT NULL,
  `V_MOVIL` varchar(20) DEFAULT NULL,
  `V_FOTO` varchar(150) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_SEDE`
--

INSERT INTO `MAE_SEDE` (`N_COD_SEDE`, `V_NOMBRE`, `N_COD_PAIS`, `N_COD_DEPARTAMENTO`, `N_COD_DISTRITO`, `V_DIRECCION`, `V_REFERENCIA`, `V_EMAIL`, `V_FONO`, `V_MOVIL`, `V_FOTO`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 'Sede Principal', NULL, NULL, NULL, 'Calle Principal', 'Ref', '', '', '999999999', '', '1', 'admweb', '2023-04-17 03:28:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_SEDE_ESPECIALISTA`
--

CREATE TABLE `MAE_SEDE_ESPECIALISTA` (
  `N_COD_SEDE` int(5) UNSIGNED NOT NULL,
  `N_COD_ESPECIALISTA` int(5) UNSIGNED NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_SEDE_ESPECIALISTA`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_TIPO_DOC`
--

CREATE TABLE `MAE_TIPO_DOC` (
  `N_COD_TIPODOC` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `N_LONGITUD` int(3) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_TIPO_DOC`
--

INSERT INTO `MAE_TIPO_DOC` (`N_COD_TIPODOC`, `V_DES_LARGA`, `V_DES_CORTA`, `N_LONGITUD`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
(00001, 'Documento Nacional de Identidad', 'DNI', 8, '1', 'admweb', '2023-04-17 02:45:38', NULL, NULL),
(00002, 'Carnet de Extranjeria', 'CARNET EXT', 12, '1', 'admweb', '2023-04-17 02:46:07', NULL, NULL),
(00003, 'Registro Unico de Contribuyentes', '	 RUC', 11, '1', 'admweb', '2023-04-17 02:46:30', NULL, NULL),
(00004, 'Pasaporte', 'Pasaporte', 12, '1', 'admweb', '2023-04-17 02:46:53', NULL, NULL),
(00005, 'Partida de Nacimiento de Identidad', 'Partida', 15, '1', 'admweb', '2023-04-17 02:47:25', NULL, NULL),
(00006, 'Otros', 'Otros', 15, '1', 'admweb', '2023-04-17 02:47:36', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_TIPO_TERAPIA`
--

CREATE TABLE `MAE_TIPO_TERAPIA` (
  `N_COD_TIPOTERAPIA` int(5) UNSIGNED ZEROFILL NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `N_DURACION_MIN` int(5) DEFAULT NULL,
  `N_PRECIO` decimal(9,2) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_TIPO_TERAPIA`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_TIPO_USUARIO`
--

CREATE TABLE `MAE_TIPO_USUARIO` (
  `V_COD_TIPO` varchar(10) NOT NULL,
  `V_DES_LARGA` varchar(150) NOT NULL,
  `V_DES_CORTA` varchar(100) NOT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_TIPO_USUARIO`
--

INSERT INTO `MAE_TIPO_USUARIO` (`V_COD_TIPO`, `V_DES_LARGA`, `V_DES_CORTA`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('PAC', 'Paciente', 'Paciente', '1', 'admweb', '2026-03-14 11:01:52', NULL, NULL),
('USER_ADM', 'Usuario Administrativo', 'Administrativo', '1', 'admweb', '2026-03-14 11:01:52', NULL, NULL),
('USER_ESP', 'Usuario Especialista', 'Especialista Medico', '1', 'admweb', '2026-03-14 11:01:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MAE_USUARIO`
--

CREATE TABLE `MAE_USUARIO` (
  `V_COD_USER` varchar(10) NOT NULL,
  `V_NOMBRES` varchar(150) NOT NULL,
  `V_EMAIL` varchar(60) DEFAULT NULL,
  `V_CLAVE` varchar(60) NOT NULL,
  `V_FOTO` varchar(150) DEFAULT NULL,
  `D_FEC_ALTA` date DEFAULT NULL,
  `D_FEC_BAJA` date DEFAULT NULL,
  `V_COD_TIPO` varchar(10) NOT NULL,
  `N_COD_REFERENCIA` int(10) DEFAULT NULL,
  `V_OBSERVACION` text DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MAE_USUARIO`
--

INSERT INTO `MAE_USUARIO` (`V_COD_USER`, `V_NOMBRES`, `V_EMAIL`, `V_CLAVE`, `V_FOTO`, `D_FEC_ALTA`, `D_FEC_BAJA`, `V_COD_TIPO`, `N_COD_REFERENCIA`, `V_OBSERVACION`, `V_FLAG_ESTADO`, `V_AUD_USR_REG`, `D_AUD_FEC_REG`, `V_AUD_USR_MOD`, `D_AUD_FEC_MOD`) VALUES
('admweb', 'Johnny Reque', 'johnnyreque@gmail.com', 'jreque21', 'admweb.jpg', '2026-03-17', NULL, 'USER_ADM', NULL, NULL, '1', 'admweb', '2026-03-14 11:01:52', NULL, NULL);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_CITA`
--

CREATE TABLE `MOV_CITA` (
  `N_COD_CITA` int(10) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_SEDE` int(5) NOT NULL,
  `N_COD_PACIENTE` int(10) NOT NULL,
  `N_COD_ESPECIALISTA` int(5) NOT NULL,
  `N_COD_TRATAMIENTO` int(5) DEFAULT NULL,
  `N_COD_TIPOTERAPIA` int(5) DEFAULT NULL,
  `D_FEC_CITA` date NOT NULL,
  `D_HORA_INICIO` time NOT NULL,
  `D_HORA_FIN` time NOT NULL,
  `V_MOT_CONSULTA` varchar(300) DEFAULT NULL,
  `V_DIAGNOSTICO` varchar(300) DEFAULT NULL,
  `V_OBSERVACION` varchar(300) DEFAULT NULL,
  `N_COD_CITA_ORIGEN` int(10) DEFAULT NULL,
  `V_MOT_REPROG` varchar(300) DEFAULT NULL,
  `V_MOT_CANC` varchar(300) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_ESTADO_CITA` char(3) NOT NULL DEFAULT 'PRO',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MOV_CITA`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_CITA_HISTORIAL`
--

CREATE TABLE `MOV_CITA_HISTORIAL` (
  `N_COD_HISTORIAL` int(10) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_CITA` int(10) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_OBSERVACION` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_COMPROBANTE`
--

CREATE TABLE `MOV_COMPROBANTE` (
  `N_COD_COMPROBANTE` int(10) NOT NULL,
  `N_COD_CITA` int(10) NOT NULL,
  `D_FEC_EMISION` date NOT NULL,
  `N_COD_MONEDA` int(5) NOT NULL,
  `N_MONTO` decimal(9,2) NOT NULL,
  `N_COD_FORMAPAGO` int(5) NOT NULL,
  `N_COD_MEDIOPAGO` int(5) DEFAULT NULL,
  `V_COMPROBANTE` varchar(500) DEFAULT NULL,
  `V_OBSERVACION` text DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_ESTADO_COMPROBANTE` char(3) NOT NULL DEFAULT 'PEN',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MOV_COMPROBANTE`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_HISTORIA_CLINICA`
--

CREATE TABLE `MOV_HISTORIA_CLINICA` (
  `N_COD_HISTORIA` int(10) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_PACIENTE` int(10) NOT NULL,
  `N_COD_CITA` int(10) DEFAULT NULL,
  `N_COD_SESION` int(10) DEFAULT NULL,
  `D_FEC_CITA` date NOT NULL,
  `V_DIAGNOSTICO` varchar(300) DEFAULT NULL,
  `V_OBSERVACION` varchar(300) DEFAULT NULL,
  `V_ANTECEDENTES` varchar(300) DEFAULT NULL,
  `V_ALERGIAS` varchar(300) DEFAULT NULL,
  `V_ENFERMEDADES` varchar(300) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MOV_HISTORIA_CLINICA`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_PAGO`
--

CREATE TABLE `MOV_PAGO` (
  `N_COD_PAGO` int(10) NOT NULL,
  `N_COD_COMPROBANTE` int(10) NOT NULL,
  `N_COD_CITA` int(10) NOT NULL,
  `D_FEC_PAGO` date NOT NULL,
  `N_COD_MONEDA` int(5) NOT NULL,
  `N_MONTO` decimal(9,2) NOT NULL,
  `N_COD_MEDIOPAGO` int(5) NOT NULL,
  `V_COMPROBANTE` varchar(500) DEFAULT NULL,
  `V_OBSERVACION` text DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MOV_PAGO`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_RECORDATORIO`
--

CREATE TABLE `MOV_RECORDATORIO` (
  `N_COD_RECORDATORIO` int(10) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_CITA` int(10) NOT NULL,
  `V_TIPO` varchar(10) NOT NULL DEFAULT 'PACIENTE',
  `D_FEC_ENVIO` datetime NOT NULL,
  `V_ESTADO_ENVIO` varchar(10) NOT NULL,
  `V_RESPUESTA` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_SESION`
--

CREATE TABLE `MOV_SESION` (
  `N_COD_SESION` int(10) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_TRATAMIENTO` int(10) NOT NULL,
  `N_COD_CITA` int(10) DEFAULT NULL,
  `N_NUM_SESION` int(5) NOT NULL,
  `D_FEC_SESION` date NOT NULL,
  `V_OBSERVACION` varchar(300) DEFAULT NULL,
  `V_EVOLUCION` varchar(300) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MOV_SESION`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `MOV_TRATAMIENTO`
--

CREATE TABLE `MOV_TRATAMIENTO` (
  `N_COD_TRATAMIENTO` int(10) UNSIGNED ZEROFILL NOT NULL,
  `N_COD_SEDE` int(5) NOT NULL,
  `N_COD_PACIENTE` int(10) NOT NULL,
  `N_COD_ESPECIALISTA` int(5) NOT NULL,
  `D_FEC_INICIO` date NOT NULL,
  `D_FEC_FIN` date DEFAULT NULL,
  `N_NUM_SESIONES` int(5) NOT NULL,
  `V_DIAGNOSTICO` varchar(300) DEFAULT NULL,
  `V_OBSERVACION` varchar(300) DEFAULT NULL,
  `V_FLAG_ESTADO` char(1) NOT NULL DEFAULT '1',
  `V_ESTADO_TRATAMIENTO` char(3) NOT NULL DEFAULT 'ACT',
  `V_AUD_USR_REG` varchar(10) NOT NULL,
  `D_AUD_FEC_REG` datetime NOT NULL,
  `V_AUD_USR_MOD` varchar(10) DEFAULT NULL,
  `D_AUD_FEC_MOD` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Volcado de datos para la tabla `MOV_TRATAMIENTO`
--

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `MAE_CONFIG_WHATSAPP`
--
ALTER TABLE `MAE_CONFIG_WHATSAPP`
  ADD PRIMARY KEY (`V_ID`);

--
-- Indices de la tabla `MAE_DEPARTAMENTO`
--
ALTER TABLE `MAE_DEPARTAMENTO`
  ADD PRIMARY KEY (`N_COD_DEPARTAMENTO`),
  ADD KEY `FK_DPTO_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_DISTRITO`
--
ALTER TABLE `MAE_DISTRITO`
  ADD PRIMARY KEY (`N_COD_DISTRITO`),
  ADD KEY `FK_DISTRITO_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_EMPRESA`
--
ALTER TABLE `MAE_EMPRESA`
  ADD PRIMARY KEY (`V_ID`);

--
-- Indices de la tabla `MAE_ESPECIALIDAD`
--
ALTER TABLE `MAE_ESPECIALIDAD`
  ADD PRIMARY KEY (`N_COD_ESPECIALIDAD`),
  ADD KEY `FK_ESPECIALIDAD_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_ESPECIALISTA`
--
ALTER TABLE `MAE_ESPECIALISTA`
  ADD PRIMARY KEY (`N_COD_ESPECIALISTA`),
  ADD KEY `FK_ESPECIALISTA_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_FORMA_PAGO`
--
ALTER TABLE `MAE_FORMA_PAGO`
  ADD PRIMARY KEY (`N_COD_FORMAPAGO`),
  ADD KEY `FK_MODPAGO_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_MEDIO_PAGO`
--
ALTER TABLE `MAE_MEDIO_PAGO`
  ADD PRIMARY KEY (`N_COD_MEDIOPAGO`),
  ADD KEY `FK_TIPOPAGO_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_MENU_NIVEL`
--
ALTER TABLE `MAE_MENU_NIVEL`
  ADD PRIMARY KEY (`N_COD_NIVEL`),
  ADD KEY `FK_NIVEL_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_MENU_OPCION`
--
ALTER TABLE `MAE_MENU_OPCION`
  ADD PRIMARY KEY (`N_COD_OPCION`),
  ADD KEY `FK_OPCION_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_MONEDA`
--
ALTER TABLE `MAE_MONEDA`
  ADD PRIMARY KEY (`N_COD_MONEDA`),
  ADD KEY `FK_MONEDA_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_PACIENTE`
--
ALTER TABLE `MAE_PACIENTE`
  ADD PRIMARY KEY (`N_COD_PACIENTE`),
  ADD KEY `FK_PACIENTE_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_PAIS`
--
ALTER TABLE `MAE_PAIS`
  ADD PRIMARY KEY (`N_COD_PAIS`),
  ADD KEY `FK_PAIS_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_PROVINCIA`
--
ALTER TABLE `MAE_PROVINCIA`
  ADD PRIMARY KEY (`N_COD_PROVINCIA`);

--
-- Indices de la tabla `MAE_ROL`
--
ALTER TABLE `MAE_ROL`
  ADD PRIMARY KEY (`V_COD_ROL`),
  ADD KEY `FK_ROL_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_ROL_OPCION`
--
ALTER TABLE `MAE_ROL_OPCION`
  ADD PRIMARY KEY (`V_COD_ROL`,`N_COD_OPCION`),
  ADD KEY `FK_ROLOPC_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_ROL_USUARIO`
--
ALTER TABLE `MAE_ROL_USUARIO`
  ADD PRIMARY KEY (`V_COD_ROL`,`V_COD_USER`),
  ADD KEY `FK_ROLUSR_USUARIO` (`V_COD_USER`),
  ADD KEY `FK_ROLUSR_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_SEDE`
--
ALTER TABLE `MAE_SEDE`
  ADD PRIMARY KEY (`N_COD_SEDE`),
  ADD KEY `FK_SEDE_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_SEDE_ESPECIALISTA`
--
ALTER TABLE `MAE_SEDE_ESPECIALISTA`
  ADD PRIMARY KEY (`N_COD_SEDE`,`N_COD_ESPECIALISTA`),
  ADD KEY `FK_SEDEINSTRUCTOR_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_TIPO_DOC`
--
ALTER TABLE `MAE_TIPO_DOC`
  ADD PRIMARY KEY (`N_COD_TIPODOC`),
  ADD KEY `FK_TIPODOC_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_TIPO_TERAPIA`
--
ALTER TABLE `MAE_TIPO_TERAPIA`
  ADD PRIMARY KEY (`N_COD_TIPOTERAPIA`),
  ADD KEY `FK_TIPOTERAPIA_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_TIPO_USUARIO`
--
ALTER TABLE `MAE_TIPO_USUARIO`
  ADD PRIMARY KEY (`V_COD_TIPO`),
  ADD KEY `FK_TIPOUSR_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MAE_USUARIO`
--
ALTER TABLE `MAE_USUARIO`
  ADD PRIMARY KEY (`V_COD_USER`);

--
-- Indices de la tabla `MOV_CITA`
--
ALTER TABLE `MOV_CITA`
  ADD PRIMARY KEY (`N_COD_CITA`),
  ADD KEY `FK_MOVCITA_USR` (`V_AUD_USR_REG`),
  ADD KEY `IDX_CITA_ESPECIALISTA_FECHA` (`N_COD_ESPECIALISTA`,`D_FEC_CITA`),
  ADD KEY `IDX_CITA_PACIENTE_FECHA` (`N_COD_PACIENTE`,`D_FEC_CITA`);

--
-- Indices de la tabla `MOV_CITA_HISTORIAL`
--
ALTER TABLE `MOV_CITA_HISTORIAL`
  ADD PRIMARY KEY (`N_COD_HISTORIAL`),
  ADD KEY `FK_MOVHISTCITA_USR` (`V_AUD_USR_REG`);

--
-- Indices de la tabla `MOV_COMPROBANTE`
--
ALTER TABLE `MOV_COMPROBANTE`
  ADD PRIMARY KEY (`N_COD_COMPROBANTE`),
  ADD KEY `FK_MOVCOMP_USR` (`V_AUD_USR_REG`),
  ADD KEY `IDX_COMP_CITA` (`N_COD_CITA`);

--
-- Indices de la tabla `MOV_HISTORIA_CLINICA`
--
ALTER TABLE `MOV_HISTORIA_CLINICA`
  ADD PRIMARY KEY (`N_COD_HISTORIA`),
  ADD KEY `FK_MOVHC_USR` (`V_AUD_USR_REG`),
  ADD KEY `IDX_HISTORIA_PACIENTE` (`N_COD_PACIENTE`),
  ADD KEY `IDX_HC_CITA` (`N_COD_CITA`),
  ADD KEY `IDX_HC_SESION` (`N_COD_SESION`);

--
-- Indices de la tabla `MOV_PAGO`
--
ALTER TABLE `MOV_PAGO`
  ADD PRIMARY KEY (`N_COD_PAGO`),
  ADD KEY `FK_MOVPAGO_USR` (`V_AUD_USR_REG`),
  ADD KEY `IDX_PAGO_CITA` (`N_COD_CITA`),
  ADD KEY `IDX_PAGO_COMPROBANTE` (`N_COD_COMPROBANTE`);

--
-- Indices de la tabla `MOV_RECORDATORIO`
--
ALTER TABLE `MOV_RECORDATORIO`
  ADD PRIMARY KEY (`N_COD_RECORDATORIO`),
  ADD KEY `IDX_RECORD_CITA` (`N_COD_CITA`);

--
-- Indices de la tabla `MOV_SESION`
--
ALTER TABLE `MOV_SESION`
  ADD PRIMARY KEY (`N_COD_SESION`),
  ADD KEY `FK_MOVSESION_USR` (`V_AUD_USR_REG`),
  ADD KEY `IDX_SESION_TRATAMIENTO` (`N_COD_TRATAMIENTO`);

--
-- Indices de la tabla `MOV_TRATAMIENTO`
--
ALTER TABLE `MOV_TRATAMIENTO`
  ADD PRIMARY KEY (`N_COD_TRATAMIENTO`),
  ADD KEY `FK_MOVTRAT_USR` (`V_AUD_USR_REG`),
  ADD KEY `IDX_TRAT_PACIENTE` (`N_COD_PACIENTE`),
  ADD KEY `IDX_TRAT_ESPECIALISTA` (`N_COD_ESPECIALISTA`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `MAE_DEPARTAMENTO`
--
ALTER TABLE `MAE_DEPARTAMENTO`
  MODIFY `N_COD_DEPARTAMENTO` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `MAE_DISTRITO`
--
ALTER TABLE `MAE_DISTRITO`
  MODIFY `N_COD_DISTRITO` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `MAE_ESPECIALIDAD`
--
ALTER TABLE `MAE_ESPECIALIDAD`
  MODIFY `N_COD_ESPECIALIDAD` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `MAE_ESPECIALISTA`
--
ALTER TABLE `MAE_ESPECIALISTA`
  MODIFY `N_COD_ESPECIALISTA` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `MAE_FORMA_PAGO`
--
ALTER TABLE `MAE_FORMA_PAGO`
  MODIFY `N_COD_FORMAPAGO` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `MAE_MEDIO_PAGO`
--
ALTER TABLE `MAE_MEDIO_PAGO`
  MODIFY `N_COD_MEDIOPAGO` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `MAE_MENU_NIVEL`
--
ALTER TABLE `MAE_MENU_NIVEL`
  MODIFY `N_COD_NIVEL` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `MAE_MENU_OPCION`
--
ALTER TABLE `MAE_MENU_OPCION`
  MODIFY `N_COD_OPCION` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `MAE_MONEDA`
--
ALTER TABLE `MAE_MONEDA`
  MODIFY `N_COD_MONEDA` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `MAE_PACIENTE`
--
ALTER TABLE `MAE_PACIENTE`
  MODIFY `N_COD_PACIENTE` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `MAE_PAIS`
--
ALTER TABLE `MAE_PAIS`
  MODIFY `N_COD_PAIS` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `MAE_SEDE`
--
ALTER TABLE `MAE_SEDE`
  MODIFY `N_COD_SEDE` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `MAE_TIPO_DOC`
--
ALTER TABLE `MAE_TIPO_DOC`
  MODIFY `N_COD_TIPODOC` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `MAE_TIPO_TERAPIA`
--
ALTER TABLE `MAE_TIPO_TERAPIA`
  MODIFY `N_COD_TIPOTERAPIA` int(5) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `MOV_CITA`
--
ALTER TABLE `MOV_CITA`
  MODIFY `N_COD_CITA` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `MOV_CITA_HISTORIAL`
--
ALTER TABLE `MOV_CITA_HISTORIAL`
  MODIFY `N_COD_HISTORIAL` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `MOV_HISTORIA_CLINICA`
--
ALTER TABLE `MOV_HISTORIA_CLINICA`
  MODIFY `N_COD_HISTORIA` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `MOV_RECORDATORIO`
--
ALTER TABLE `MOV_RECORDATORIO`
  MODIFY `N_COD_RECORDATORIO` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `MOV_SESION`
--
ALTER TABLE `MOV_SESION`
  MODIFY `N_COD_SESION` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `MOV_TRATAMIENTO`
--
ALTER TABLE `MOV_TRATAMIENTO`
  MODIFY `N_COD_TRATAMIENTO` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `MAE_DEPARTAMENTO`
--
ALTER TABLE `MAE_DEPARTAMENTO`
  ADD CONSTRAINT `FK_DPTO_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_DISTRITO`
--
ALTER TABLE `MAE_DISTRITO`
  ADD CONSTRAINT `FK_DISTRITO_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_ESPECIALIDAD`
--
ALTER TABLE `MAE_ESPECIALIDAD`
  ADD CONSTRAINT `FK_ESPECIALIDAD_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_ESPECIALISTA`
--
ALTER TABLE `MAE_ESPECIALISTA`
  ADD CONSTRAINT `FK_ESPECIALISTA_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_FORMA_PAGO`
--
ALTER TABLE `MAE_FORMA_PAGO`
  ADD CONSTRAINT `FK_MODPAGO_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_MEDIO_PAGO`
--
ALTER TABLE `MAE_MEDIO_PAGO`
  ADD CONSTRAINT `FK_TIPOPAGO_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_MENU_NIVEL`
--
ALTER TABLE `MAE_MENU_NIVEL`
  ADD CONSTRAINT `FK_NIVEL_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_MENU_OPCION`
--
ALTER TABLE `MAE_MENU_OPCION`
  ADD CONSTRAINT `FK_OPCION_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_MONEDA`
--
ALTER TABLE `MAE_MONEDA`
  ADD CONSTRAINT `FK_MONEDA_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_PACIENTE`
--
ALTER TABLE `MAE_PACIENTE`
  ADD CONSTRAINT `FK_PACIENTE_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_PAIS`
--
ALTER TABLE `MAE_PAIS`
  ADD CONSTRAINT `FK_PAIS_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_ROL`
--
ALTER TABLE `MAE_ROL`
  ADD CONSTRAINT `FK_ROL_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_ROL_OPCION`
--
ALTER TABLE `MAE_ROL_OPCION`
  ADD CONSTRAINT `FK_ROLOPC_ROL` FOREIGN KEY (`V_COD_ROL`) REFERENCES `MAE_ROL` (`V_COD_ROL`),
  ADD CONSTRAINT `FK_ROLOPC_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_ROL_USUARIO`
--
ALTER TABLE `MAE_ROL_USUARIO`
  ADD CONSTRAINT `FK_ROLUSR_ROL` FOREIGN KEY (`V_COD_ROL`) REFERENCES `MAE_ROL` (`V_COD_ROL`),
  ADD CONSTRAINT `FK_ROLUSR_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`),
  ADD CONSTRAINT `FK_ROLUSR_USUARIO` FOREIGN KEY (`V_COD_USER`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_SEDE`
--
ALTER TABLE `MAE_SEDE`
  ADD CONSTRAINT `FK_SEDE_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_SEDE_ESPECIALISTA`
--
ALTER TABLE `MAE_SEDE_ESPECIALISTA`
  ADD CONSTRAINT `FK_SEDEINSTRUCTOR_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_TIPO_DOC`
--
ALTER TABLE `MAE_TIPO_DOC`
  ADD CONSTRAINT `FK_TIPODOC_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_TIPO_TERAPIA`
--
ALTER TABLE `MAE_TIPO_TERAPIA`
  ADD CONSTRAINT `FK_TIPOTERAPIA_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MAE_TIPO_USUARIO`
--
ALTER TABLE `MAE_TIPO_USUARIO`
  ADD CONSTRAINT `FK_TIPOUSR_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_CITA`
--
ALTER TABLE `MOV_CITA`
  ADD CONSTRAINT `FK_MOVCITA_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_CITA_HISTORIAL`
--
ALTER TABLE `MOV_CITA_HISTORIAL`
  ADD CONSTRAINT `FK_MOVHISTCITA_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_COMPROBANTE`
--
ALTER TABLE `MOV_COMPROBANTE`
  ADD CONSTRAINT `FK_MOVCOMP_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_HISTORIA_CLINICA`
--
ALTER TABLE `MOV_HISTORIA_CLINICA`
  ADD CONSTRAINT `FK_MOVHC_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_PAGO`
--
ALTER TABLE `MOV_PAGO`
  ADD CONSTRAINT `FK_MOVPAGO_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_SESION`
--
ALTER TABLE `MOV_SESION`
  ADD CONSTRAINT `FK_MOVSESION_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);

--
-- Filtros para la tabla `MOV_TRATAMIENTO`
--
ALTER TABLE `MOV_TRATAMIENTO`
  ADD CONSTRAINT `FK_MOVTRAT_USR` FOREIGN KEY (`V_AUD_USR_REG`) REFERENCES `MAE_USUARIO` (`V_COD_USER`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
