<?php

namespace TDN\Bundle\RedactionBundle\Entity;

use Doctrine\ORM\EntityRepository;

use TDN\Bundle\DocumentBundle\Entity\DocumentRepository;

/**
 * SelectionShoppingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SelectionShoppingRepository extends DocumentRepository
{
	  public function findMostRecent ($limite, $panel = NULL) {
        return parent::findMostRecentDocument($limite, 'SELECTION_PUBLIEE', $panel);
    }


    public function findPage ($page, $size = 50, $all = true) {
    	$offset = ($page - 1) * $size;
    	$qb = $this->createQueryBuilder('u');
    	$query = $qb->select('u');
    	if ($all !== true && is_numeric($all)) {
    		$query = $query->where($qb->expr()->eq('u.lnAuteur', ':id'))
    					   ->setParameter('id', $all);
    	}
        $query = $query->orderBy('u.datePublication', 'DESC')
            		   ->setFirstResult($offset);
        if ($size !== 0) {
        	$query = $query->setMaxResults($size);
        }
        $query = $query->getQuery();
        // ->where($qb->expr()->like('u.statut', $qb->expr()->literal('ARTICLE_PUBLIE')))

         $last = $query->getResult();
         return $last;
    }

}
