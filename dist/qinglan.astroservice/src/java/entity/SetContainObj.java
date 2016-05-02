/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "set_contain_obj")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "SetContainObj.findAll", query = "SELECT s FROM SetContainObj s"),
    @NamedQuery(name = "SetContainObj.findBySetContainObjId", query = "SELECT s FROM SetContainObj s WHERE s.setContainObjId = :setContainObjId")})
public class SetContainObj implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "set_contain_obj_id")
    private Long setContainObjId;
    @JoinColumn(name = "set_src_id", referencedColumnName = "set_id")
    @ManyToOne(optional = false)
    private SetInfo setSrcId;
    @JoinColumn(name = "object_tar_id", referencedColumnName = "object_id")
    @ManyToOne(optional = false)
    private ObjectInfo objectTarId;

    public SetContainObj() {
    }

    public SetContainObj(Long setContainObjId) {
        this.setContainObjId = setContainObjId;
    }

    public Long getSetContainObjId() {
        return setContainObjId;
    }

    public void setSetContainObjId(Long setContainObjId) {
        this.setContainObjId = setContainObjId;
    }

    public SetInfo getSetSrcId() {
        return setSrcId;
    }

    public void setSetSrcId(SetInfo setSrcId) {
        this.setSrcId = setSrcId;
    }

    public ObjectInfo getObjectTarId() {
        return objectTarId;
    }

    public void setObjectTarId(ObjectInfo objectTarId) {
        this.objectTarId = objectTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (setContainObjId != null ? setContainObjId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof SetContainObj)) {
            return false;
        }
        SetContainObj other = (SetContainObj) object;
        if ((this.setContainObjId == null && other.setContainObjId != null) || (this.setContainObjId != null && !this.setContainObjId.equals(other.setContainObjId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.SetContainObj[ setContainObjId=" + setContainObjId + " ]";
    }
    
}
