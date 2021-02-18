<?php

namespace App\CycloBranch;

use App\Base\ReferenceHelper;
use App\Entity\Sequence;
use App\Enum\SequenceEnum;
use App\Smiles\Parser\Accept;
use App\Smiles\Parser\Reject;

class SequenceCycloBranch extends AbstractCycloBranch {

    const FILE_NAME = './uploads/sequences.txt';

    const TYPE = 0;
    const NAME = 1;
    const FORMULA = 2;
    const MASS = 3;
    const SEQUENCE = 4;
    const N_TERMINAL_MODIFICATION = 5;
    const C_TERMINAL_MODIFICATION = 6;
    const B_TERMINAL_MODIFICATION = 7;
    const REFERENCE = 8;
    const LENGTH = 9;

    /**
     * @param string $line
     * @return Accept|Reject|Reject
     * @see AbstractCycloBranch::parse()
     */
    public function parse(string $line) {
        // TODO
        $arResult = $this->validateLine($line, false);
        if ($arResult === false) {
            return self::reject();
        }

//        $type = $this->validateType($arResult[self::TYPE]);
//        $nModificationId = $cModificationId = $bModificationId = null;
//        $nModification = $this->modificationDatabase->findByName($arResult[self::N_TERMINAL_MODIFICATION]);
//        if (!empty($nModification)) {
//            $nModificationId = $nModification['id'];
//        }
//        $cModification = $this->modificationDatabase->findByName($arResult[self::C_TERMINAL_MODIFICATION]);
//        if (!empty($cModification)) {
//            $cModificationId = $cModification['id'];
//        }
//        $bModification = $this->modificationDatabase->findByName($arResult[self::B_TERMINAL_MODIFICATION]);
//        if (!empty($bModification)) {
//            $bModificationId = $bModification['id'];
//        }
//
//        $blockAcronyms = SequenceHelper::getBlockAcronyms($arResult[self::SEQUENCE]);
//        $blockIds = [];
//        foreach ($blockAcronyms as $blockAcronym) {
//            $block = $this->blockDatabase->findByAcronym($blockAcronym);
//            if (!empty($block)) {
//                $blockIds[] = $block['id'];
//            }
//        }
//
//        $smiles = '';
//        $reference = new ReferenceTO();
//        $referenceParser = new ReferenceParser();
//        $referenceResult = $referenceParser->parse($arResult[self::REFERENCE]);
//        if ($referenceResult->isAccepted()) {
//            if ($referenceResult->getResult()->database === "SMILES") {
//                $smiles = $referenceResult->getResult()->identifier;
//            } else {
//                $reference->database = $referenceResult->getResult()->database;
//                $reference->identifier = $referenceResult->getResult()->identifier;
//            }
//        }
//
//        $sequenceTO = new SequenceTO(
//            $reference->database,
//            $arResult[self::NAME],
//            $smiles,
//            $arResult[self::FORMULA],
//            $arResult[self::MASS],
//            $reference->identifier,
//            $arResult[self::SEQUENCE],
//            $type
//        );
//
//        $sequenceTO->nModification = $nModificationId;
//        $sequenceTO->cModification = $cModificationId;
//        $sequenceTO->bModification = $bModificationId;
//
//        return new Accept([
//            SequenceTO::TABLE_NAME => $sequenceTO,
//            'blockIds' => $blockIds,
//        ], '');
    }

    /**
     * @see AbstractCycloBranch::save()
     */
    protected function save(array $arTos) {
        // TODO
//        $referenceDatabase = $arTos[SequenceTO::TABLE_NAME]->database;
//        if ($referenceDatabase === ServerEnum::PUBCHEM || $referenceDatabase === ServerEnum::CHEBI) {
//            $finder = FinderFactory::getFinder($referenceDatabase);
//            $findResult = null;
//            $outArResult = [];
//            try {
//                $findResult = $finder->findByIdentifier($arTos[SequenceTO::TABLE_NAME]->identifier, $outArResult);
//            } catch (BadTransferException $e) {
//                Logger::log(LoggerEnum::WARNING, "Block not found");
//            }
//            if ($findResult === ResultEnum::REPLY_OK_ONE) {
//                $arTos[SequenceTO::TABLE_NAME]->smiles = $outArResult['smile'];
//            }
//        }
//        $exThrown = $exThrownSequence = true;
//        $this->database->startTransaction();
//        $sequenceId = null;
//        try {
//            $sequenceId = $this->database->insert($arTos[SequenceTO::TABLE_NAME]);
//        } catch (UniqueConstraintException $e) {
//            Logger::log(LoggerEnum::WARNING, "Sequence already in database");
//            $exThrownSequence = true;
//        }
//        $blockIds = $arTos['blockIds'];
//        foreach ($blockIds as $blockId) {
//            $blockToSequence = new BlockToSequenceTO($blockId, $sequenceId);
//            try {
//                $this->controller->blockToSequence_model->insert($blockToSequence);
//            } catch (UniqueConstraintException $e) {
//                Logger::log(LoggerEnum::WARNING, "Block to sequence already in database. Sequence id: " . $sequenceId . " block id: " . $blockId);
//                $exThrown = true;
//            } catch (\Error $e) {
//                $this->database->endTransaction();
//                return;
//            }
//        }
//        if ($exThrown || $exThrownSequence) {
//            $this->database->commit();
//        } else {
//            $this->database->rollback();
//        }
    }

    private function validateType($type) {
        if (isset(SequenceEnum::$backValues[$type])) {
            return SequenceEnum::$backValues[$type];
        }
        return false;
    }

    /**
     * @see AbstractCycloBranch::reject()
     */
    public static function reject() {
        return new Reject('Not match sequence in right format');
    }

    /**
     * @see AbstractCycloBranch::download()
     */
    public function download(): string {
        $this->data = '';
        /** @var Sequence[] $arResult */
        $arResult = $this->repository->findBy(['container' => $this->containerId]);
        if (!empty($arResult)) {
            foreach ($arResult as $sequence) {
                $this->data .= $sequence->getSequenceType() . self::TABULATOR
                    . $sequence->getSequenceName() . self::TABULATOR
                    . $sequence->getSequenceFormula() . self::TABULATOR
                    . $sequence->getSequenceMass() . self::TABULATOR
                    . $sequence->getSequence() . self::TABULATOR
                    . (($sequence->getNModification() !== null) ? $sequence->getNModification()->getModificationName() : '') . self::TABULATOR
                    . (($sequence->getCModification() !== null) ? $sequence->getCModification()->getModificationName() : '') . self::TABULATOR
                    . (($sequence->getBModification() !== null) ? $sequence->getBModification()->getModificationName() : '') . self::TABULATOR
                    . ReferenceHelper::reference($sequence->getSource(), $sequence->getIdentifier(), $sequence->getSequenceSmiles())
                    . PHP_EOL;
            }
        }
        return $this->data;
    }

    /**
     * @see AbstractCycloBranch::getFileName()
     */
    protected function getFileName() {
        return self::FILE_NAME;
    }

    /**
     * @see AbstractCycloBranch::getLineLength()
     */
    protected function getLineLength() {
        return self::LENGTH;
    }

}
