<?php

namespace TDN\Bundle\CauseuseBundle\Entity;

use Doctrine\ORM\EntityRepository;

use TDN\Bundle\DocumentBundle\Entity\DocumentRepository;

/**
 * ReponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReponseRepository extends DocumentRepository
{

	public function findMostRecent ($limite, $panel = NULL) {
      return parent::findMostRecentDocument ($limite, 'REPONSE_PUBLIEE', $panel);
   }

   public function findMostLiked ($limite, $panel = NULL) {
      return parent::findMostLikedDocument ($limite, 'REPONSE_PUBLIEE', $panel);
   }
}
