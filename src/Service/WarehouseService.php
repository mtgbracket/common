<?php


namespace Mtgbracket\Service;


use Google\Cloud\Firestore\DocumentReference;
use Google\Cloud\Firestore\DocumentSnapshot;
use Google\Cloud\Firestore\FieldValue;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * Class WarehouseService
 * @package Mtgbracket\Service
 */
class WarehouseService
{
    /** @var DocumentReference */
    private $document;

    /**
     * @param int $userId
     * @param string $field
     * @return mixed|null
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function getValue(int $userId, string $field)
    {
        return $this->getDocument($userId)->snapshot()->get($field);
    }

    /**
     * @param int $userId
     * @param string $field
     * @param string $value
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function setValue(int $userId, string $field, string $value)
    {
        $this->getDocument($userId)->set([
            $field => $value
        ], ['merge' => true]);
    }

    /**
     * @param int $userId
     * @param string $field
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    public function incrementValue(int $userId, string $field)
    {
        $this->getDocument($userId)->update([
            [
                'path' => $field,
                'value' => FieldValue::increment(1)
            ]
        ]);
    }

    /**
     * @param $userId
     * @return DocumentSnapshot
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    private function getDocument($userId): DocumentReference
    {
        /**
         * if document not already cached, pull from warehouse.
         */
        if($this->document === null) {
            $client = new FirestoreClient([
                'projectId' => $_ENV['GOOGLE_PROJECT_ID'],
                'keyFilePath' => $_ENV['GOOGLE_APPLICATION_CREDENTIALS']
            ]);

            $this->document = $client->collection('achievements-warehouse')->document($userId);
        }

        return $this->document;
    }
}