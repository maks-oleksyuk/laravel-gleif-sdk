<?php

declare(strict_types=1);

namespace Oleksyuk\Gleif\Resources;

use Oleksyuk\Gleif\Enums\EntityCategory;
use Oleksyuk\Gleif\Enums\ModificationType;
use Oleksyuk\Gleif\Enums\PolicyConformityFlag;
use Oleksyuk\Gleif\Enums\RecordType;
use Oleksyuk\Gleif\Enums\RegistrationStatus;
use Oleksyuk\Gleif\Requests\LeiRecords\FieldModifications\FieldModifications;
use Oleksyuk\Gleif\Requests\LeiRecords\Isins\Isins;
use Oleksyuk\Gleif\Requests\LeiRecords\LeiIssuer\LeiIssuer;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\AllLeiRecords;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl1Information\LeiRecordById;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Children\DirectChildRelationship;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Children\DirectChildren;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Children\UltimateChildRelationship;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Children\UltimateChildren;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\DirectParentLeiRecord;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\DirectParentRelationship;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\DirectParentReportingException;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\UltimateParentLeiRecord;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\UltimateParentRelationship;
use Oleksyuk\Gleif\Requests\LeiRecords\Lvl2RelationshipInformation\Parents\UltimateParentReportingException;
use Oleksyuk\Gleif\Requests\LeiRecords\RelatedLeiRecords\AssociatedEntity;
use Oleksyuk\Gleif\Requests\LeiRecords\RelatedLeiRecords\ManagingLou;
use Oleksyuk\Gleif\Requests\LeiRecords\RelatedLeiRecords\SuccessorEntity;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class LeiRecordResource extends BaseResource
{
    // Level 1 — LEI Records

    /**
     * @param  string[]  $lei  up to 300 LEIs
     *
     * @throws \InvalidArgumentException when there are more than 300 LEIs
     */
    public function all(
        array $lei = [],
        ?string $bic = null,
        ?string $isin = null,
        ?string $owns = null,
        ?string $ownedBy = null,
        ?EntityCategory $entityCategory = null,
        ?string $entityLegalName = null,
        ?string $entityLegalAddressCountry = null,
        ?RegistrationStatus $registrationStatus = null,
        ?PolicyConformityFlag $conformityFlag = null,
        ?string $fulltext = null,
        ?string $sort = null,
        ?int $pageSize = null,
        ?int $pageNumber = null,
    ): Response {
        return $this->connector->send(new AllLeiRecords(
            lei: $lei,
            bic: $bic,
            isin: $isin,
            owns: $owns,
            ownedBy: $ownedBy,
            entityCategory: $entityCategory,
            entityLegalName: $entityLegalName,
            entityLegalAddressCountry: $entityLegalAddressCountry,
            registrationStatus: $registrationStatus,
            conformityFlag: $conformityFlag,
            fulltext: $fulltext,
            sort: $sort,
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }

    public function find(string $id): Response
    {
        return $this->connector->send(new LeiRecordById($id));
    }

    public function isins(string $id, ?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new Isins($id, $pageSize, $pageNumber));
    }

    public function leiIssuer(string $id): Response
    {
        return $this->connector->send(new LeiIssuer($id));
    }

    // Level 2 — Parents

    public function directParent(string $id): Response
    {
        return $this->connector->send(new DirectParentLeiRecord($id));
    }

    public function directParentRelationship(string $id): Response
    {
        return $this->connector->send(new DirectParentRelationship($id));
    }

    public function directParentReportingException(string $id): Response
    {
        return $this->connector->send(new DirectParentReportingException($id));
    }

    public function ultimateParent(string $id): Response
    {
        return $this->connector->send(new UltimateParentLeiRecord($id));
    }

    public function ultimateParentRelationship(string $id): Response
    {
        return $this->connector->send(new UltimateParentRelationship($id));
    }

    public function ultimateParentReportingException(string $id): Response
    {
        return $this->connector->send(new UltimateParentReportingException($id));
    }

    // Level 2 — Children

    public function directChildren(string $id, ?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new DirectChildren($id, $pageSize, $pageNumber));
    }

    public function directChildRelationships(string $id, ?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new DirectChildRelationship($id, $pageSize, $pageNumber));
    }

    public function ultimateChildren(string $id, ?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new UltimateChildren($id, $pageSize, $pageNumber));
    }

    public function ultimateChildRelationships(string $id, ?int $pageSize = null, ?int $pageNumber = null): Response
    {
        return $this->connector->send(new UltimateChildRelationship($id, $pageSize, $pageNumber));
    }

    // Related

    public function associatedEntity(string $id): Response
    {
        return $this->connector->send(new AssociatedEntity($id));
    }

    public function managingLou(string $id): Response
    {
        return $this->connector->send(new ManagingLou($id));
    }

    public function successorEntity(string $id): Response
    {
        return $this->connector->send(new SuccessorEntity($id));
    }

    // Field Modifications

    public function fieldModifications(
        string $id,
        ?ModificationType $modificationType = null,
        ?RecordType $recordType = null,
        ?string $field = null,
        ?string $date = null,
        ?int $pageSize = null,
        ?int $pageNumber = null,
    ): Response {
        return $this->connector->send(new FieldModifications(
            id: $id,
            modificationType: $modificationType,
            recordType: $recordType,
            field: $field,
            date: $date,
            pageSize: $pageSize,
            pageNumber: $pageNumber,
        ));
    }
}
