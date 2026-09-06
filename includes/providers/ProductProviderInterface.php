<?php
/**
 * Interface ProductProviderInterface
 * 
 * Declares the standard contract for all external e-commerce API providers.
 */
interface ProductProviderInterface {
    /**
     * Executes a product search query on the respective provider's API.
     * 
     * @param string $query The product keyword/search phrase.
     * @return array A list of normalized product structures.
     */
    public function search($query);
}
