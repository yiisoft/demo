import * as React from 'react';
import {DataGrid, GridColDef, GridRenderCellParams} from '@mui/x-data-grid';
import {Typography} from '@mui/material';

type GetEntry = {
    key: string;
    count: number;
};
type SetEntry = {
    key: string;
    value: object;
    ttl: any;
};
type CachePanelProps = {
    data: {
        cache: {
            get: GetEntry[];
            set: SetEntry[];
        };
    };
};

const columnsGet: GridColDef[] = [
    {field: 'key', headerName: 'Key', flex: 1},
    {field: 'count', headerName: 'Count'},
];

const columnsSet: GridColDef[] = [
    {field: 'key', headerName: 'Key'},
    {
        field: 'value',
        headerName: 'Value',
        flex: 1,
        renderCell: (params: GridRenderCellParams) => <pre>{JSON.stringify(params.value, null, 4)}</pre>,
    },
    {field: 'ttl', headerName: 'TTL'},
    {field: 'count', headerName: 'Count'},
];

const CachePanel = ({data}: CachePanelProps) => {
    return (
        <>
            <Typography>Cache keys were requested</Typography>
            <DataGrid
                autoHeight
                rows={data.cache.get}
                columns={columnsGet}
                getRowId={(row) => row.key}
                getRowHeight={() => 'auto'}
            />
            <Typography>Cache keys were changed</Typography>
            <DataGrid
                autoHeight
                rows={data.cache.set}
                columns={columnsSet}
                getRowId={(row) => row.key}
                getRowHeight={() => 'auto'}
            />
        </>
    );
};

export default CachePanel;
