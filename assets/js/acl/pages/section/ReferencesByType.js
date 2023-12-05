import React from 'react'
import { FormControl, Table } from 'react-bootstrap'
import ButtonWithLoading from '../../components/ButtonWithLoading'
import ReferenceItem from '../../components/ReferenceItem'
import SuccessIcon from '../../components/SuccessIcon'
import useSuccessIcon from '../../hooks/useSuccessIcon'
import useTextFilter from '../../hooks/useTextFilter'
import { useReferences, useSaveReferences } from '../../hooks/references'
import { useIsMutating } from 'react-query'

const getItemContents = item => {
  return [
    item.resource,
    item.name,
    item.initialResource,
    item.route,
    item.routePath
  ].join('')
}

export default function ReferencesByType ({ isLoading, references: serverReferences, showHide = true }) {
  const { references, updateReference } = useReferences(serverReferences)
  const { textSearch, handleTextSearchChange, filterByText } = useTextFilter()
  const filteredReferences = filterByText(references, getItemContents)

  const saveBtn = <SaveButton isLoading={isLoading} references={references} />

  return (
    <div>

      {references.length > 8 && saveBtn}

      <FormControl
        className="mb-2"
        placeholder="Search..."
        value={textSearch}
        onChange={handleTextSearchChange} />

      <div className="table-responsive">
        <Table bordered size="sm">
          <thead>
            <tr>
              <th>Apply</th>
              <th style={{ minWidth: 200 }}>Resource</th>
              <th>Reference</th>
              <th>Route Name</th>
              <th>Route Path</th>
              {showHide && (<th>Hide</th>)}
            </tr>
          </thead>
          <tbody>
            {filteredReferences.map(item => (
              <ReferenceItem
                key={item.identifier}
                item={item}
                onEdit={updateReference}
                showHide={showHide}
              />
            ))}
            {filteredReferences.length === 0 && (
              <tr>
                <td className="text-center" colSpan={100}>No items found</td>
              </tr>
            )}
          </tbody>
        </Table>
      </div>

      {saveBtn}

    </div>
  )
}

function SaveButton ({ isLoading, references }) {
  const selectedCount = references.filter(({ selected, hidden }) => selected || hidden).length
  const isSaving = useIsMutating({ mutationKey: ['references'] }) > 0
  const { isShowSuccessIcon, showSuccessIcon } = useSuccessIcon()
  const { saveReferences } = useSaveReferences()

  async function save () {
    await saveReferences(references)
    showSuccessIcon()
  }

  return (
    <div>
      <ButtonWithLoading
        disabled={selectedCount === 0 || isLoading || isSaving}
        active={selectedCount > 0}
        isLoading={isSaving}
        label="Apply Changes"
        className="mb-2"
        onClick={save} />
      <SuccessIcon isShow={isShowSuccessIcon} />
    </div>
  )
}
