<?php

namespace nongkuschoolubol;

class SunlightsuplyApiDB
{
    const TABLE_NAME    = 'wpdg_ss_api_products';
    const TABLE_FAMILIES= 'wpdg_ss_api_families';

    const INSERT_PART   = 500;

    protected $api;
    protected $wpdb;
    protected $errors = [];



    public function __construct($apiKey, $apiSecret)
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->api  = new SunlightsuplyApi($apiKey, $apiSecret);
    }



    public function getApi()
    {
        return $this->api;
    }



    public function uploadProducts()
    {
        $products = $this->getApi()->getSingleProducts();

        if (!empty($products) && !$this->getApi()->hasErrors()) {
            $i = 0;
            $insertData = [];
            $familiesId = $this->wpdb->get_col("
                SELECT family_id FROM " . self::TABLE_FAMILIES . ";
            ");

            $this->wpdb->query("TRUNCATE TABLE " . self::TABLE_NAME);

            foreach ($products as $product) {

                if (in_array($product['PartFamilyId'], $familiesId) && $product['EachPrice'] != 0)
                    $insertData[] = '('
                        . (int)esc_sql($product['Id']) . ', '
                        . '"' . esc_sql($product['Name']) . '", '
                        . (int)esc_sql($product['CategoryId']) . ', '
                        . '"' . esc_sql($product['CategoryName']) . '", '
                        . '"' . esc_sql($product['CategoryWebId']) . '", '
                        . '"' . esc_sql($product['WebDescription']) . '", '
                        . '"' . esc_sql($product['ImageThumb']) . '", '
                        . '"' . esc_sql($product['ImageMedium']) . '", '
                        . (float)esc_sql($product['EachPrice']) . ', '
                        . (int)esc_sql($product['StockTotal']) . ', '
                        . (float)esc_sql($product['EachWeight']) . ', '
                        . (float)esc_sql($product['EachLength']) . ', '
                        . (float)esc_sql($product['EachWidth']) . ', '
                        . (float)esc_sql($product['EachHeight']) . ', '
                        . (int)esc_sql($product['PartFamilyId']) . ', '
                        . '"' . esc_sql($product['PartFamilyName']) . '", '
                        . '"' . esc_sql($product['PartFamilyWebId']) . '", '
                        . (int)esc_sql($product['CaseQuantity']) . ', '
                        . '"' . esc_sql(serialize($product)) . '"'
                        . ')';

                if (!($i % self::INSERT_PART) && !empty($insertData)) {
                    $this->insertProductsPart($insertData);
                    $insertData = [];
                }
                $i++;
            }

            $this->insertProductsPart($insertData);
        } elseif (empty($products)) {
            $this->errors[] = 'Failed to get list of products';
        }
    }

    protected function insertProductsPart(array &$values)
    {
        if (empty($values))
            return false;

        return $this->wpdb->query("
            INSERT INTO " . self::TABLE_NAME. "
                (
                    product_id, product_name, category_id, category_name,
                    category_web_id, description, image_thumbnail,
                    image_medium, price, quantity, product_weight,
                    product_length, product_width, product_height, family_id,
                    family_name, family_web_id, case_quantity, api_data
                ) VALUES " . implode(', ', $values) . " 
            ON DUPLICATE KEY UPDATE
                product_id = VALUES(product_id), 
                product_name = VALUES(product_name), 
                category_id = VALUES(category_id), 
                category_name = VALUES(category_name),
                category_web_id = VALUES(category_web_id), 
                description = VALUES(description), 
                image_thumbnail = VALUES(image_thumbnail),
                image_medium = VALUES(image_medium), 
                price = VALUES(price), 
                quantity = VALUES(quantity), 
                product_weight = VALUES(product_weight),
                product_length = VALUES(product_length), 
                product_width = VALUES(product_width), 
                product_height = VALUES(product_height), 
                family_id = VALUES(family_id),
                family_name = VALUES(family_name),
                family_web_id = VALUES(family_web_id),
                case_quantity = VALUES(case_quantity),
                api_data = VALUES(api_data)
        ;");
    }



    public function uploadFamilies()
    {
        $families = $this->getApi()->getFamilies();

        if (!empty($families) && !$this->getApi()->hasErrors()) {
            $this->wpdb->query("TRUNCATE TABLE " . self::TABLE_FAMILIES);
            $i = 0;
            $insertData = [];
            foreach ($families as $family) {
                if ($family['ViewPublic'] != 0)
                    $insertData[] = '('
                        . (int)esc_sql($family['Id']) . ', '
                        . '"' . esc_sql($family['Name']) . '", '
                        . '"' . esc_sql($family['WebId']) . '", '
                        . '"' . esc_sql($family['MetaDescription']) . '", '
                        . (int)esc_sql($family['CategoryId']) . ', '
                        . '"' . esc_sql($family['CategoryName']) . '", '
                        . '"' . esc_sql($family['CategoryWebId']) . '", '
                        . '"' . esc_sql($family['ImageThumb']) . '", '
                        . '"' . esc_sql($family['ImageMedium']) . '", '
                        . '"' . esc_sql(serialize($family)) . '"'
                        . ')';

                if (!($i % self::INSERT_PART) && !empty($insertData)) {
                    $this->insertFamiliesPart($insertData);
                    $insertData = [];
                }
                $i++;
            }

            $this->insertFamiliesPart($insertData);
        } elseif (empty($products)) {
            $this->errors[] = 'Failed to get list of families';
        }
    }

    protected function insertFamiliesPart(array &$values)
    {
        if (empty($values))
            return false;

        return $this->wpdb->query("
            INSERT INTO " . self::TABLE_FAMILIES. "
                (
                    family_id, family_name, family_web_id,
                    family_description, category_id, category_name,
                    category_web_id, image_medium, image_thumbnail,
                    api_data
                ) VALUES " . implode(', ', $values) . " 
            ON DUPLICATE KEY UPDATE
                family_id = VALUES(family_id), 
                family_name = VALUES(family_name), 
                family_web_id = VALUES(family_web_id),
                family_description = VALUES(family_description), 
                category_id = VALUES(category_id), 
                category_name = VALUES(category_name),
                category_web_id = VALUES(category_web_id), 
                image_medium = VALUES(image_medium), 
                image_thumbnail = VALUES(image_thumbnail),
                api_data = VALUES(api_data)
        ;");
    }



    public function getProducts(
        $limit = false,
        $offset = 0,
        $excludedCategories = [],
        $familiesId = []
    ) {
        $where = [];

        if (!empty($excludedCategories))
            $where[] = "category_web_id NOT IN ('" . implode('\', \'', $excludedCategories) . "')";
        if (!empty($familiesId))
            $where[] = "family_id IN ('" . implode('\', \'', $familiesId) . "')";

        return $this->wpdb->get_results("
            SELECT 
                product_id, product_name, description, price, quantity, product_weight,
                product_length, product_width, product_height, category_web_id, image_medium,
                family_id, family_name, family_web_id, api_data
            FROM " . self::TABLE_NAME . "
            " . (!empty($where) ? "WHERE " . implode(' AND ', $where) : "") . "
            " . ($limit ? "LIMIT " . $limit . " OFFSET " . $offset : "") . "
        ;", ARRAY_A);
    }



    public function getProductsNum($excludedCategories = []) {
        return $this->wpdb->get_results("
            SELECT count(*) AS cnt
            FROM " . self::TABLE_NAME . "
            " . (!empty($excludedCategories) ? "WHERE category_web_id NOT IN ('" . implode('\', ', $excludedCategories) . "')" : "") . "
        ;", ARRAY_A)[0]['cnt'];
    }

    public function getFamiliesNum($excludedCategories = []) {
        return $this->wpdb->get_results("
            SELECT count(*) AS cnt
            FROM " . self::TABLE_FAMILIES . "
            " . (!empty($excludedCategories) ? "WHERE category_web_id NOT IN ('" . implode('\', ', $excludedCategories) . "')" : "") . "
        ;", ARRAY_A)[0]['cnt'];
    }

    public function getFamilies(
        $limit = false,
        $offset = 0,
        $excludedCategories = []
    ) {
        $families   = [];
        $familiesId = [];
        
        $result = $this->wpdb->get_results("
            SELECT 
                family_id, family_name, family_description, family_web_id,
                category_id, category_name, category_web_id,
                image_medium, image_thumbnail
            FROM " . self::TABLE_FAMILIES . "
            " . (!empty($excludedCategories) ? "WHERE category_web_id NOT IN ('" . implode('\', \'', $excludedCategories) . "')" : "") . "
            " . ($limit ? "LIMIT " . $limit . " OFFSET " . $offset : "") . "
        ;", ARRAY_A);

        if (!empty($result)) {
            foreach ($result as $key => $item) {
                $families[$item['family_id']] = [
                    'family_id'             => $item['family_id'],
                    'family_name'           => $item['family_name'],
                    'family_description'    => $item['family_description'],
                    'category_id'           => $item['category_id'],
                    'category_name'         => $item['category_name'],
                    'category_web_id'       => $item['category_web_id'],
                    'image_thumbnail'       => $item['image_thumbnail'],
                    'image_medium'          => $item['image_medium'],
                ];

                $familiesId[] = $item['family_id'];
                unset($result[$key]);
            }

            $products = $this->getProducts(false, 0, $excludedCategories, $familiesId);

            foreach ($products as $key => $product) {
                $families[$product['family_id']]['items'][] = $product;
                unset($products[$key]);
            }
        }

        return $families;
    }



    public function setParsedImages($limit = false, $offset = 0)
    {
        $linkPart = 'https://www.hawthornegc.com/shop/product/';

        $familyWebId = $this->wpdb->get_col("
            SELECT family_web_id FROM " . self::TABLE_FAMILIES ."
            " . ($limit ? "LIMIT " . $limit . " OFFSET " . $offset : "") . "
        ;");

        $productsId = $this->wpdb->get_col("
            SELECT product_id FROM " . self::TABLE_NAME ."
            WHERE family_web_id IN ('" . implode("', '", $familyWebId) . "');
        ;");

        $i = 0;
        foreach ($familyWebId as $webId) {
            $images = SunlightsuplyImageParser::getImages($linkPart . $webId);

            if (!empty($images))
                foreach ($images as $productId => $image)
                    if (in_array($productId, $productsId)) {
                        $insertData[] = '('
                            . (int)esc_sql($productId) . ', '
                            . '"' . esc_sql(serialize($image)) . '"'
                            . ')';
                    }

            if (!($i % self::INSERT_PART) && !empty($insertData)) {
                $this->insertImagesPart($insertData);
                $insertData = [];
            }
            $i++;
        }

        if (!empty($insertData))
            $this->insertImagesPart($insertData);
    }

    protected function insertImagesPart(&$values)
    {
        if (empty($values))
            return false;

        return $this->wpdb->query("
            INSERT INTO " . self::TABLE_NAME. "
                (product_id, product_parsed_images) 
            VALUES " . implode(', ', $values) . " 
            ON DUPLICATE KEY UPDATE
                product_id = VALUES(product_id), 
                product_parsed_images = VALUES(product_parsed_images)
        ;");
    }



    public function uploadAll()
    {
        $this->uploadFamilies();
        $this->uploadProducts();
        $this->deleteFamiliesWithoutProducts();
    }


    public function deleteFamiliesWithoutProducts()
    {
        $familyId = $this->wpdb->get_col("
            SELECT 
	            f.family_id, 
	            sum(p.price) AS price 
            FROM wpdg_ss_api_families AS f
            LEFT JOIN wpdg_ss_api_products AS p ON f.family_id = p.family_id
            WHERE price = 0 OR price IS NULL
            GROUP BY f.family_id;
        ");

        $this->wpdb->query("
            DELETE FROM " . self::TABLE_FAMILIES .  "
            WHERE family_id IN (" . implode(', ', $familyId) . ")
        ");
    }


    public function getCategories()
    {
        return $this->wpdb->get_results("
            SELECT 
                category_id, 
                category_name, 
                category_web_id 
            FROM " . self::TABLE_FAMILIES . "
            GROUP BY category_id
        ;", ARRAY_A);
    }

    public function getErrors()
    {
        $errors = array_merge(
            $this->getApi()->getErrors(),
            $this->errors
        );

        return $errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors) || $this->getApi()->hasErrors();
    }

}