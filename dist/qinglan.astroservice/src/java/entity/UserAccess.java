package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.xml.bind.annotation.XmlRootElement;

@Entity
@Table(name = "user_access_control")
@XmlRootElement
public class UserAccess implements Serializable
{
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @Column(name = "user_access_id")
    private int user_access_id;
    
    @Column(name = "user_id")
    private Integer user_id;
    
    @Column(name = "create_access")
    private int create_access;
    
    @Column(name = "modify_access")
    private int modify_access;
    
    @Column(name = "delete_access")
    private int delete_access;
    
    @Column(name = "view_access")
    private int view_access;
    
    @Column(name = "operator_type")
    private String operator_type;
    
    @Column(name = "operator_id")
    private Integer operator_id;
    
    public UserAccess()
    {
    }
    
    public UserAccess(int user_access_id, int user_id, int create_access, int modify_access, 
            int delete_access, int view_access, String operator_type, int operator_id)
    {
        this.user_access_id = user_access_id;
        this.user_id = user_id;
        this.create_access = create_access;
        this.modify_access = modify_access;
        this.delete_access = delete_access;
        this.view_access = view_access;
        this.operator_type = operator_type;
        this.operator_id = operator_id;
    }
    
    public int getUserAccessId()
    {
        return user_access_id;
    }
    
    public void setUserAccessId(int id)
    {
        user_access_id = id;
    }
    
    public Integer getUserId()
    {
        return user_id;
    }
    
    public void setUserId(Integer id)
    {
        user_id = id;
    }
    
    public int getCreateAccess()
    {
        return create_access;
    }
    
    public void setCreateAccess(int access)
    {
        create_access = access;
    }
    
    public int getModifyAccess()
    {
        return modify_access;
    }
    
    public void setModifyAccess(int access)
    {
        modify_access = access;
    }
    
    public int getDeleteAccess()
    {
        return delete_access;
    }
    
    public void setDeleteAccess(int access)
    {
        delete_access = access;
    }
    
    public int getViewAccess()
    {
        return view_access;
    }
    
    public void setViewAccess(int access)
    {
        view_access = access;
    }
    
    public String getOperatorType()
    {
        return operator_type;
    }
    
    public void setOperatorType(String type)
    {
        operator_type = type;
    }
    
    public Integer getOperatorId()
    {
        return operator_id;
    }
    
    public void setOperatorId(Integer id)
    {
        operator_id = id;
    }
}