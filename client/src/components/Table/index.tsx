import React from 'react';
import './Table.css';

const Table = <T extends Record<string, unknown>>({
  columns,
  data,
  keyExtractor,
  emptyMessage = 'No data available',
  className = '',
  addButton,
}: TableProps<T>) => {
  return (
    <div className={`table-container ${className}`}>
      <table className="custom-table">
        <thead>
          <tr>
            {columns.map((column) => (
              <th 
                key={column.key} 
                style={column.width ? { width: column.width } : undefined}
              >
                {column.header}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {data.length > 0 ? (
            data.map((item) => (
              <tr key={keyExtractor(item)}>
                {columns.map((column) => (
                  <td key={`${keyExtractor(item)}-${column.key}`}>
                    {column.render ? column.render(item) : (item[column.key] as React.ReactNode)}
                  </td>
                ))}
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan={columns.length} className="empty-table-message">
                {emptyMessage}
              </td>            </tr>
          )}
        </tbody>
        {addButton && (
          <tfoot>
            <tr>
              <td colSpan={columns.length} className="table-footer-cell">
                {addButton}
              </td>
            </tr>
          </tfoot>
        )}
      </table>
    </div>
  );
};

export default Table;
