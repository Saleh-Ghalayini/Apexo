import React from 'react';

export interface TableColumn<T> {
  key: string;
  header: string;
}

export interface TableProps<T> {
  columns: TableColumn<T>[];
  data: T[];
  keyExtractor: (item: T) => string;
}

const Table = <T extends Record<string, unknown>>({
  columns,
  data,
  keyExtractor,
}: TableProps<T>) => {
  return (
    <table>
      <thead>
        <tr>
          {columns.map((column) => (
            <th key={column.key}>{column.header}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {data.map((item) => (
          <tr key={keyExtractor(item)}>
            {columns.map((column) => (
              <td key={column.key}>{item[column.key] as React.ReactNode}</td>
            ))}
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Table;