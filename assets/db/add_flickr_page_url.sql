-- Agregar columna para guardar la URL de la página de Flickr
-- Esto permite que los usuarios puedan ir directamente a la página de Flickr
-- en lugar de solo ver la imagen

ALTER TABLE VRE_REPOSITORIO_FOTOS
ADD COLUMN FLICKR_PAGE_URL VARCHAR(500) DEFAULT NULL
COMMENT 'URL de la página de Flickr (ej: https://flic.kr/p/2pLPh8f)';

-- Crear índice para búsquedas rápidas
CREATE INDEX idx_flickr_page ON VRE_REPOSITORIO_FOTOS(FLICKR_PAGE_URL);
