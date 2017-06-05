<?php

namespace Czesio\NestablePageBundle\Repository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPageMetaByLocale($slug, $locale) {

        $query = $this->createQueryBuilder('p')
            ->select('p', 'pm')
            ->Join('p.pageMetas','pm')
            ->where('p.isPublished = :isPublished')
            ->andWhere('pm.locale = :locale')
            ->andWhere('p.slug = :slug')
            ->setParameter('isPublished', '1')
            ->setParameter('locale', $locale)
            ->setParameter('slug', $slug)
            ->getQuery();

        return $query->getOneOrNullResult();

    }

    public function findParent() {

        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.isPublished = :isPublished')
            ->andWhere('p.parent is null')
            ->setParameter('isPublished', '1')
            ->orderBy('p.sequence', 'asc')
            ->getQuery();

        return $query->getResult();

    }

    /**
     * reorder element based on user input
     * @param  int $id       id of element dragged
     * @param  int $parentId parent id
     * @param  int $position new position relative to parent id. 0 is first position
     * @return array          array([string] message, [boolean] success)
     */
    public function reorderElement($id, $parentId, $position)
    {
        // step 1: get all siblings based on old location. update the seq
        $old_item = $this->findOneById($id);

        if ($old_item === null) {
            $old_parent_id = '';
        }
        else {
            $old_parent_id = ($old_item->getParent() === null) ? '' : $old_item->getParent()->getId();
        }


        // if old parent and new parent is the same, user moving in same level.
        // dont need to update old parent
        if ($old_parent_id != $parentId) {
            $old_children = $this->findBy(
                array('parent' => $old_parent_id),
                array('sequence' => 'ASC')
            );
            $seq = 0;

            foreach ($old_children as $oc) {
                $or = $this->findOneById($oc->getId());
                if ($old_item->getSequence() != $or->getSequence()) {
                    $or->setSequence($seq);
                    $this->getEntityManager()->persist($or);
                    $seq++;
                }
            }
        }

        $new_children = $this->findBy(
            array('parent' => $parentId),
            array('sequence' => 'ASC')
        );
        $seq = 0;

        $ir = $this->findOneById($id);


        if (!is_null($parentId)) {
            $parent = $this->findOneById($parentId);
            if ($ir !== null) {
                $ir->setParent($parent);
            }
        }
        else {
            if ($ir !== null) {
                $ir->setParent();
            }
        }
        foreach ($new_children as $nc) {
            // if the id is the same, it means user moves in same level

            if ($old_parent_id == $parentId) {
                // if in same level, we just need to swap position
                // get id of element with the current position then swap it
                $nr = $this->findBy(
                    array('sequence' => $position, 'parent' => $parentId)
                );

                $nr[0]->setSequence($ir->getSequence());
                $this->getEntityManager()->persist($nr[0]);
                $ir->setSequence($position);
                $this->getEntityManager()->persist($ir);
                break;
            }
            // user drag from one level to the next, it is a new addition
            else {

                if ($position == $seq) {
                    $ir->setSequence($seq);
                    $this->getEntityManager()->persist($ir);
                    $seq++;
                }

                $nr = $this->findOneById($nc->getId());
                $nr->setSequence($seq);
                $this->getEntityManager()->persist($nr);

            }

            $seq++;
        }

        // if its the last entry and user moved to new level
        if ($old_parent_id != $parentId && $position == count($new_children)) {
            $ir->setSequence($seq);
            $this->getEntityManager()->persist($ir);
        }

        $message = '';
        $success = true;

        // step 3: run a loop, insert the new element and update the seq
        try {
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear(); // prevent doctrine from caching
            $message = 'flash_reorder_edit_success';
        }
        catch (\Exception $e) {
            // $message = $e->getMessage();
            $message = 'Cannot reorder element.';
            $success = false;
        }

        return array($message, $success);
    }
}
