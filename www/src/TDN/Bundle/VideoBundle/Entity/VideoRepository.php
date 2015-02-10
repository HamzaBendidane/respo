<?php

namespace TDN\Bundle\VideoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use TDN\Bundle\DocumentBundle\Entity\DocumentRepository;

/**
 * VideoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VideoRepository extends DocumentRepository
{
		public function findMostRecentOwn ($limite, $rubrique = NULL) {
		$qb = $this->createQueryBuilder('u');
		$qexpr = $qb->expr();
		$query = $qb->select('u');
		if ($rubrique != NULL) {
			$query = $query->innerjoin('u.rubriques', 'r')
						   ->where($qexpr->andX(
						   		$qexpr->like('u.statut', $qb->expr()->literal('VIDEO_PUBLIEE')),
						   		$qexpr->like('r.slug', ':rubrique')
						   	))
						   ->setParameter('rubrique', $rubrique);

		} else {
			$query = $query->where($qexpr()->like('u.statut', $qb->expr()->literal('VIDEO_PUBLIEE')));
		}
		$query = $query->orderBy('u.datePublication', 'DESC')
	        		   ->setMaxResults($limite)
	        		   ->getQuery();

	     $last = $query->getResult();
	     return $last;
	}

	public function findMostRecent ($limite, $panel = NULL) {
		return parent::findMostRecentDocument($limite, 'VIDEO_PUBLIEE', $panel);
	}

	public function findMostRecentWithKeys ($limite, $keys) {
		if (empty($keys)) {
			return array();
		}

		return parent::findMostRecentDocumentWithKeys($limite, 'VIDEO_PUBLIEE', $keys);
	}

	public function findLimitedAll ($limite, $offset) {
		$qb = $this->createQueryBuilder('u');
		$query = $qb->select('u')
	        		->orderBy('u.datePublication', 'DESC')
	        		->setFirstResult($offset*($limite-1))
	        		->setMaxResults($limite)
	        		->getQuery()
	        		->useResultCache(true, 900);

	     $last = $query->getResult();
	     return $last;
	}

	public function searchBy ($where, $limite = NULL, $offset = NULL) {
		if (!is_array($where)) return false;

		$champs = array_keys($where);
		$qb = $this->createQueryBuilder('u');
		$query = $qb->select('u')
					->where($qb->expr()->like('u.'.$champs[0], $qb->expr()->literal('%'.$where[$champs[0]].'%')));
		if ((integer)$limite > 0) {
			$query = $query->setMaxResults($limite);
		}
		if ((integer)$offset > 0) {
			$query = $query->setFirstResult($offset);
		}
		$query = $query->getQuery();

		$items =$query->getResult();
		return $items;
	}

	public function searchByAuteur ($chaine, $limite = NULL, $offset = NULL) {
		if ($chaine == '') return array();
		
		$qb = $this->createQueryBuilder('u');
		$query = $qb->select('u')
					->innerjoin('u.lnAuteur', 'a')
					->where($qb->expr()->orX(
						$qb->expr()->like('a.username', $qb->expr()->literal('%'.$chaine.'%')),
						$qb->expr()->like('a.nom', $qb->expr()->literal('%'.$chaine.'%')),
						$qb->expr()->like('a.prenom', $qb->expr()->literal('%'.$chaine.'%'))));
		if ((integer)$limite > 0) {
			$query = $query->setMaxResults($limite);
		}
		if ((integer)$offset > 0) {
			$query = $query->setFirstResult($offset);
		}
		$query = $query->getQuery();

		$items =$query->getResult();
		return $items;
	}

	public function count($critere = 'tdn') {
        $qb = $this->createQueryBuilder('v');
		$query = $qb->select('v.idDocument');

		if ($critere == 'all') {
			// $query = $query->where(1);
		} elseif ($critere != 'tdn') {
			$query = $query->innerjoin('v.rubriques', 'r')
						   ->leftjoin('r.rubriqueParente', 'p')
				    		->where($qb->expr()->andX(
				    			$qb->expr()->like('v.statut', $qb->expr()->literal('VIDEO_PUBLIEE')),
				    			$qb->expr()->orX(
				    				$qb->expr()->like('r.slug', ':rubrique'),
				    				$qb->expr()->like('p.slug', ':rubrique')
				    			)))
				    		->setParameter('rubrique', $critere);
		} else {
			$query = $query->where($qb->expr()->like('v.statut', $qb->expr()->literal('VIDEO_PUBLIEE')));
		}

		$query = $query->groupBy('v.idDocument')
					   ->getQuery()
					   ->useResultCache(true, 3600);

        $nombre = $query->getResult();
        // print_r(count($nombre)); die;
        return count($nombre);
    }

}