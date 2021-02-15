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
     * @return DocumentReference
     * @throws \Google\Cloud\Core\Exception\GoogleException
     */
    private function getDocument($userId): DocumentReference
    {
        $client = new FirestoreClient([
            'projectId' => $_ENV['GOOGLE_PROJECT_ID'],
            'keyFilePath' => $_ENV['GOOGLE_APPLICATION_CREDENTIALS']
        ]);

        $document = $client->collection('achievements-warehouse')->document($userId);

        /**
         * if a document hasn't been started for this user yet, initialize it.
         */
        if(!$document->snapshot()->exists()) {
            $document->set([
                'user_id' => $userId
            ]);
        }

        return $document;
    }
}