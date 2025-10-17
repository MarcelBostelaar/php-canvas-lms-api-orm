{
    "filename": "Domain.php",
    "fields": [],
    "fieldsNullable": [],
    "plurals": [
        "Domains"
    ]
}array(
    0: Stmt_Namespace(
        name: Name(
            name: CanvasApiLibrary\Models
        )
        stmts: array(
            0: Stmt_Use(
                type: TYPE_NORMAL (1)
                uses: array(
                    0: UseItem(
                        type: TYPE_UNKNOWN (0)
                        name: Name(
                            name: CanvasApiLibrary\Models\Utility\ModelInterface
                        )
                        alias: null
                    )
                )
            )
            1: Stmt_Class(
                attrGroups: array(
                )
                flags: FINAL (32)
                name: Identifier(
                    name: Domain
                )
                extends: null
                implements: array(
                    0: Name_FullyQualified(
                        name: CanvasApiLibrary\Models\Utility\ModelInterface
                    )
                )
                stmts: array(
                    0: Stmt_ClassMethod(
                        attrGroups: array(
                        )
                        flags: PUBLIC (1)
                        byRef: false
                        name: Identifier(
                            name: __construct
                        )
                        params: array(
                            0: Param(
                                attrGroups: array(
                                )
                                flags: PUBLIC | READONLY (65)
                                type: Identifier(
                                    name: string
                                )
                                byRef: false
                                variadic: false
                                var: Expr_Variable(
                                    name: domain
                                )
                                default: null
                                hooks: array(
                                )
                            )
                        )
                        returnType: null
                        stmts: array(
                        )
                    )
                    1: Stmt_ClassMethod(
                        attrGroups: array(
                        )
                        flags: PUBLIC (1)
                        byRef: false
                        name: Identifier(
                            name: getUniqueId
                        )
                        params: array(
                        )
                        returnType: Identifier(
                            name: mixed
                        )
                        stmts: array(
                            0: Stmt_Return(
                                expr: Expr_PropertyFetch(
                                    var: Expr_Variable(
                                        name: this
                                    )
                                    name: Identifier(
                                        name: domain
                                    )
                                )
                            )
                        )
                    )
                    2: Stmt_Property(
                        attrGroups: array(
                        )
                        flags: PUBLIC | STATIC (9)
                        type: Identifier(
                            name: array
                        )
                        props: array(
                            0: PropertyItem(
                                name: VarLikeIdentifier(
                                    name: plurals
                                )
                                default: Expr_Array(
                                    items: array(
                                        0: ArrayItem(
                                            key: null
                                            value: Scalar_String(
                                                value: Domains
                                            )
                                            byRef: false
                                            unpack: false
                                        )
                                    )
                                )
                            )
                        )
                        hooks: array(
                        )
                    )
                )
            )
        )
    )
)